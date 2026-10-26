<?php

use App\Database\Migrations\CreateAccessControl;
use App\Libraries\Authorization;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Services;

require_once APPPATH . 'Database/Migrations/2026-10-26-180000_CreateAccessControl.php';

final class AccessControlTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private $connection;
    private Authorization $access;
    private $forge;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = Database::connect('tests');
        $this->forge = Database::forge('tests');
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 40],
            'is_active' => ['type' => 'INT', 'default' => 1],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
        foreach ([['admin@example.invalid', 'admin'], ['reader@example.invalid', 'sales_rep'],
            ['samuel.nunez@theblancgroup.com', 'sales_rep'], ['carlos.sarmiento@forhumancapital.mx', 'sales_rep'],
            ['danya.cruz@forhumancapital.mx', 'sales_rep']] as [$email, $role]) {
            $this->connection->table('users')->insert(['full_name' => 'Test user', 'email' => $email, 'password_hash' => password_hash('Test-password-2026', PASSWORD_DEFAULT), 'role' => $role]);
        }
        (new CreateAccessControl($this->forge))->up();
        // Minimal unrelated layout fixtures; access behavior uses the real controllers/views.
        foreach ([
            'prospects' => ['id INTEGER PRIMARY KEY', 'company_name VARCHAR(100)'],
            'prospect_activities' => ['id INTEGER PRIMARY KEY', 'prospect_id INT', 'user_id INT', 'is_completed INT', 'due_date DATETIME'],
            'eventos' => ['id INTEGER PRIMARY KEY', 'estatus VARCHAR(30)', 'fecha_evento DATETIME'],
            'notificaciones' => ['id INTEGER PRIMARY KEY', 'user_id INT', 'leido INT', 'created_at DATETIME'],
        ] as $table => $fields) {
            $this->forge->addField($fields);
            $this->forge->createTable($table);
        }
        $this->access = new Authorization($this->connection);
        Services::injectMock('authorization', $this->access);
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        foreach (['notificaciones', 'eventos', 'prospect_activities', 'prospects', 'access_user_roles', 'access_role_permissions', 'access_permissions', 'access_roles', 'users'] as $table) {
            $this->forge->dropTable($table, true);
        }
        parent::tearDown();
    }

    private function signIn(int $id): void
    {
        $_SESSION = ['id' => $id, 'name' => 'Test user', 'isLoggedIn' => true, 'role' => 'admin'];
        $this->access->refresh();
    }

    private function role(string $slug): int
    {
        return (int) $this->connection->table('access_roles')->where('slug', $slug)->get()->getRow('id');
    }

    private function csrfPost(string $path, array $body)
    {
        return $this->post($path, $body + [csrf_token() => csrf_hash()]);
    }

    public function testLegacyExceptionsAreConvertedToRolesAndChannel(): void
    {
        $this->signIn(3);
        $this->assertTrue($this->access->can('carga.modificar'));
        $this->assertFalse($this->access->can('vacantes.ver'));
        $this->assertSame('Optimización', $this->access->user()['commercial_channel']);
        $this->signIn(4);
        $this->assertTrue($this->access->can('vacantes.ver'));
        $this->assertFalse($this->access->can('carga.ver'));
        $this->signIn(5);
        $this->assertTrue($this->access->can('vacantes.modificar'));
        $this->assertTrue($this->access->can('prospectos.ver_todos'));
        $this->assertTrue($this->access->can('carga.ver'));
        $this->assertFalse($this->access->isAdmin());
    }

    public function testPermissionsCombineAndRevocationAppliesWithoutLoggingOut(): void
    {
        $this->access->setUserRoles(2, [$this->role('captura'), $this->role('reclutamiento')], 'Otros');
        $this->signIn(2);
        $this->assertTrue($this->access->can('vacantes.modificar'));
        $this->assertTrue($this->access->can('carga.ver'));
        $this->assertFalse($this->access->isAdmin(), 'A forged session role cannot grant admin rights.');
        $this->access->saveRole($this->role('reclutamiento'), 'Reclutamiento', ['vacantes.ver']);
        $this->assertTrue($this->access->can('vacantes.ver'));
        $this->assertFalse($this->access->can('vacantes.modificar'));
    }

    public function testReadonlyUserCannotAccessAdminOrWriteEndpoint(): void
    {
        $this->access->setUserRoles(2, [$this->role('reclutamiento')], null);
        $this->access->saveRole($this->role('reclutamiento'), 'Consulta', ['vacantes.ver']);
        $this->withSession(['id' => 2, 'isLoggedIn' => true, 'role' => 'admin']);
        $this->get('usuarios/accesos')->assertStatus(403);
        $this->post('vacantes/actualizar', ['id' => 999, 'estatus' => 'Cerrada'])->assertStatus(403);
        $this->assertFalse($this->access->can('usuarios.administrar'));
        $labels = array_column(array_merge(...array_values($this->access->menu())), 'label');
        $this->assertNotContains('Alta de Vacante', $labels);
        $this->assertNotContains('Roles y accesos', $labels);
    }

    public function testAdministratorCanRenderBothAccessAndCreationScreens(): void
    {
        $this->withSession(['id' => 1, 'isLoggedIn' => true]);
        $page = $this->get('usuarios/accesos');
        $page->assertStatus(200);
        $page->assertSee('Configurar roles');
        $page = $this->get('usuarios/nuevo');
        $page->assertStatus(200);
        $page->assertSee('El usuario tendrá estos accesos');
    }

    public function testUserCreationStoresSelectedRoles(): void
    {
        $this->withSession(['id' => 1, 'isLoggedIn' => true]);
        $response = $this->csrfPost('usuarios/guardar', [
            'full_name' => 'New colleague', 'email' => 'new@example.invalid', 'password' => 'Safe-test-password',
            'roles' => [$this->role('captura'), $this->role('reclutamiento')], 'commercial_channel' => 'Soluciones',
        ]);
        $response->assertRedirectTo(base_url('usuarios'));
        $user = $this->connection->table('users')->where('email', 'new@example.invalid')->get()->getRowArray();
        $this->assertNotNull($user);
        $this->assertCount(2, $this->access->roleIds((int) $user['id']));
        $this->assertTrue(password_verify('Safe-test-password', $user['password_hash']));
        $this->assertSame('Soluciones', $user['commercial_channel']);
    }

    public function testCsrfIsRequiredForAdministrativeChanges(): void
    {
        $this->withSession(['id' => 1, 'isLoggedIn' => true]);
        try {
            $this->post('usuarios/accesos/roles', ['name' => 'Injected role', 'permissions' => ['vacantes.ver']]);
            $this->fail('A CSRF-free request must be rejected.');
        } catch (\CodeIgniter\Security\Exceptions\SecurityException $exception) {
            $this->assertSame(403, $exception->getCode());
        }
        $this->assertSame(0, $this->connection->table('access_roles')->where('name', 'Injected role')->countAllResults());
    }

    public function testAdministratorCannotRemoveOwnAdministratorRole(): void
    {
        $this->signIn(1);
        $this->expectException(DomainException::class);
        $this->access->setUserRoles(1, [$this->role('sales_rep')], null);
    }

    public function testAdministratorRoleCannotBeEdited(): void
    {
        $this->expectException(DomainException::class);
        $this->access->saveRole($this->role('admin'), 'Reduced admin', []);
    }

    public function testInvalidRolesAreRejectedWithoutChangingAssignments(): void
    {
        $previous = $this->access->roleIds(2);
        try {
            $this->access->setUserRoles(2, [999999], null);
            $this->fail('An unknown role must be rejected.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame($previous, $this->access->roleIds(2));
        }
    }

    public function testInactiveAndDeletedUsersLoseAccessImmediately(): void
    {
        $this->signIn(1);
        $this->assertTrue($this->access->isAdmin());
        $this->connection->table('users')->where('id', 1)->update(['is_active' => 0]);
        $this->access->refresh();
        $this->assertFalse($this->access->can('vacantes.ver'));
        $this->assertFalse($this->access->isAdmin());
        $this->connection->table('users')->where('id', 1)->update(['is_active' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        $this->access->refresh();
        $this->assertNull($this->access->user());
    }

    public function testModifyImpliesViewAndUnknownPermissionIsRejected(): void
    {
        $this->access->saveRole($this->role('captura'), 'Captura', ['vacantes.modificar']);
        $this->signIn(3);
        $this->assertTrue($this->access->can('vacantes.ver'));
        $this->assertTrue($this->access->can('vacantes.modificar'));
        $this->expectException(InvalidArgumentException::class);
        $this->access->saveRole($this->role('captura'), 'Invalid', ['usuarios.administrar']);
    }

    public function testRoleWithoutPermissionsHasNoFallbackAccess(): void
    {
        $this->access->saveRole($this->role('captura'), 'Sin pantallas', []);
        $this->signIn(3);
        $this->assertSame([], $this->access->menu());
        $this->assertSame('sin-acceso', $this->access->landingPage());
        $this->assertFalse($this->access->can('carga.ver'));
    }

    public function testLastActiveAdministratorCannotBeDemoted(): void
    {
        $this->signIn(2);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Debe permanecer al menos un administrador activo.');
        $this->access->setUserRoles(1, [$this->role('sales_rep')], null);
    }

    public function testAnonymousRequestsCannotReadOrWriteProtectedScreens(): void
    {
        $this->withSession([]);
        $this->get('usuarios/accesos')->assertRedirectTo(base_url('/'));
        $this->post('vacantes/actualizar', ['id' => 1])->assertRedirectTo(base_url('/'));
    }

    public function testEveryConfiguredRouteHasAnExplicitAuthorizationPolicy(): void
    {
        $routes = service('routes')->loadRoutes();
        $catalog = config(\Config\Access::class);
        foreach (['get', 'post'] as $verb) {
            $this->assertNotEmpty($routes->getRoutes($verb), 'The real application routes must be loaded.');
            foreach ($routes->getRoutes($verb) as $handler) {
                if (!is_string($handler) || !str_contains($handler, '::')) {
                    continue;
                }
                [$class, $method] = explode('::', $handler, 2);
                $class = basename(str_replace('\\', '/', $class));
                $method = explode('/', $method)[0];
                if (in_array($class, ['Auth', 'Usuarios', 'Accesos', 'Perfil', 'Notificaciones'], true)
                    || $class === 'Vacantes' && in_array($method, ['tvPipeline', 'getVacantesJson'], true)) {
                    continue;
                }
                $this->assertArrayHasKey($method, $catalog->actions[$class] ?? [], $handler);
                $this->assertArrayHasKey($catalog->actions[$class][$method], $catalog->permissions(), $handler);
            }
        }
    }
}
