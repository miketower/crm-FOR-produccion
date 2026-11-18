<?php

namespace App\Controllers;

/** Read-only visual proposal. The figures below are illustrative. */
class MatrizFullSkills extends BaseController
{
    public function index()
    {
        $profiles = $this->demoProfiles();
        $requested = $this->request->getGet('perfil');
        $selected = is_string($requested) && isset($profiles[$requested]) ? $requested : 'danya';

        return view('matriz_full_skills/index', array_merge($this->viewData, [
            'profiles' => $profiles,
            'selected' => $selected,
            'profile' => $profiles[$selected],
        ]));
    }

    private function demoProfiles(): array
    {
        return [
            'danya' => [
                'name' => 'Danya', 'initials' => 'DA', 'role' => 'CRM y seguimiento del embudo',
                'color' => 'blue', 'intro' => 'Visibilidad y seguimiento de cada oportunidad, desde la cita hasta la operación.',
                'indicators' => [
                    ['Resultado', 'Citas agendadas que se realizan', 'Citas realizadas respecto a las agendadas durante el mes, en todos los canales.', '92% · 46 citas', '≥ 90% y 50 citas realizadas', '600 citas realizadas', '600 citas', 'progreso'],
                    ['Resultado', 'Avance del embudo hasta operación', 'Conversión de cita a propuesta, de propuesta a cierre y de cierre a operación.', '40% · 21% · 91%', '≥ 42% · ≥ 20% · ≥ 90%', '250 propuestas y 50 cierres operando', '250 propuestas, 50 cierres e ingreso', 'progreso'],
                    ['Desarrollo', 'CRM actualizado y confiable', 'Registros completos y capturados en ≤ 24 horas; auditoría semanal.', '96% de registros', '≥ 95% completos y a tiempo', '≥ 95% promedio', 'Datos confiables para decidir', 'cumplido'],
                    ['Desarrollo', 'Seguimiento en excelencia', 'Oportunidades con siguiente paso y fecha, alertas oportunas y reporte consolidado.', '98% · 95% · 1 reporte', '100% con siguiente paso; 100% alertas ≤ 24 h; 1 reporte', '12 reportes', 'Que no se pierdan oportunidades', 'atencion'],
                ],
            ],
            'david' => [
                'name' => 'David', 'initials' => 'DV', 'role' => 'Propuestas, cierre y operación',
                'color' => 'peach', 'intro' => 'De la propuesta al cierre y su transición efectiva a operación.',
                'indicators' => [
                    ['Resultado', 'Cierres comerciales e IB cerrado', 'Cierres firmados en el mes y su ingreso bruto en los distintos canales.', '3 cierres · $3.6 M', '3–4 cierres y ≥ $3.4 M IB', '42–43 cierres y $40.8 M IB', '50 cierres y meta de ingreso, junto con Querétaro', 'cumplido'],
                    ['Resultado', 'Cierres que llegan a operación', 'Altas operativas respecto a cierres comerciales y tiempo hasta su operación.', '92% en ≤ 30 días', '≥ 90% operando en ≤ 30 días', '≥ 90%', 'Ingreso real, no solo firmado', 'cumplido'],
                    ['Desarrollo', 'Propuestas elaboradas y presentadas a tiempo', 'Días hábiles entre la cita y la propuesta presentada, y propuestas del mes.', '4 días · 17 propuestas', '≤ 5 días y 18 propuestas', '212 propuestas', '250 propuestas, junto con Querétaro', 'progreso'],
                    ['Desarrollo', 'Seguimiento a propuestas presentadas', 'Contacto cada ≤ 7 días y propuestas con más de 45 días sin movimiento.', '92% con contacto · 12% estancadas', '100% con contacto ≤ 7 días; ≤ 10% estancadas', 'Mismo estándar', 'Conversión propuesta → cierre ≥ 20%', 'atencion'],
                ],
            ],
            'aurora' => [
                'name' => 'Aurora', 'initials' => 'AU', 'role' => 'Asociados FOR y Prosper House',
                'color' => 'teal', 'intro' => 'Desarrollo de la comunidad y generación de oportunidades por canal.',
                'indicators' => [
                    ['Resultado', 'Citas de Asociados, Prosper House y Método Action', 'Citas realizadas por canal y por ejecutor durante el mes.', '18 citas', '20 citas', '240 citas', '40% de las 600 citas', 'progreso'],
                    ['Resultado', 'Calidad de la cita del canal', 'Citas del canal que avanzan a propuesta respecto al total de citas del canal.', '43% · 8 propuestas', '≥ 42% y 8 propuestas originadas', '100 propuestas originadas', '250 propuestas', 'cumplido'],
                    ['Desarrollo', 'Crecimiento de la comunidad', 'Nuevos asociados y Prosper vinculados con convenio firmado.', '3 vinculaciones', '≥ 3 vinculaciones con convenio', '36 vinculaciones', 'Capacidad futura del canal', 'cumplido'],
                    ['Desarrollo', 'Activación y reactivación de asociados', 'Comunidad con citas, inactivos reactivados y conversión de Método Action a cita.', '60% · 4 reactivados · 31%', '≥ 60% activos; ≥ 5 reactivados; Action → cita ≥ 30%', '60 reactivados', 'Volumen de citas del canal', 'progreso'],
                ],
            ],
            'lu' => [
                'name' => 'Lu', 'initials' => 'LU', 'role' => 'Eventos FOR',
                'color' => 'purple', 'intro' => 'Citas, prospección y nuevas vinculaciones generadas desde eventos.',
                'indicators' => [
                    ['Resultado', 'Citas generadas en eventos', 'Citas realizadas con origen en eventos internos y externos.', '13 citas', '12–13 citas', '150 citas', '25% de las 600 citas', 'cumplido'],
                    ['Resultado', 'Calidad de la cita de eventos', 'Citas de eventos que avanzan a propuesta respecto al total de citas de eventos.', '45% · 5 propuestas', '≥ 42% y 5 propuestas originadas', '63 propuestas originadas', '250 propuestas', 'cumplido'],
                    ['Desarrollo', 'Eventos con prospección', 'Eventos realizados, citas por evento y seguimiento a asistentes calificados.', '2 eventos · 4 citas/evento · 90%', '≥ 3 eventos; ≥ 4 citas por evento; 100% seguimiento ≤ 48 h', '36 eventos', 'Volumen de citas', 'progreso'],
                    ['Desarrollo', 'Vinculación de asociados desde eventos', 'Asociados, Prosper y alianzas vinculados en eventos y entregados a Aurora.', '2 vinculaciones', '≥ 2 vinculaciones', '24 vinculaciones', 'Alimenta el canal Asociados', 'cumplido'],
                ],
            ],
            'yuridia' => [
                'name' => 'Yuridía', 'initials' => 'YU', 'role' => 'FOR Querétaro · comercial',
                'color' => 'slate', 'intro' => 'Prospección, propuestas y cierres comerciales de la plaza Querétaro.',
                'indicators' => [
                    ['Resultado', 'Citas Querétaro', 'Citas realizadas durante el mes en la plaza.', '7 citas', '7–8 citas', '90 citas', '15% de las 600 citas', 'cumplido'],
                    ['Resultado', 'Propuestas, cierres e IB Querétaro', 'Propuestas presentadas, cierres e ingreso bruto de la plaza.', '3 propuestas · 1 cierre', '3 propuestas y 1 cierre cada 6 semanas', '38 propuestas; 7–8 cierres; $7.2 M IB', '15% de propuestas, cierres e ingreso', 'progreso'],
                    ['Desarrollo', 'Prospección activa', 'Prospectos calificados contactados y conversión de contacto a cita.', '34 contactos · 26%', '≥ 30 contactos y ≥ 25% a cita', '360 contactos', 'Volumen de citas', 'cumplido'],
                    ['Desarrollo', 'Seguimiento de propuestas Querétaro', 'Días entre cita y propuesta y contacto a propuestas cada ≤ 7 días.', '6 días · 93% con contacto', '≤ 5 días y 100% con contacto ≤ 7 días', 'Mismo estándar', 'Conversión propuesta → cierre ≥ 20%', 'atencion'],
                ],
            ],
        ];
    }
}
