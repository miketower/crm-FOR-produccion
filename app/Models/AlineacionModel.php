<?php
namespace App\Models;
use CodeIgniter\Model;

class AlineacionModel extends Model {
    protected $table = 'alineacion_estrategica';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'empresa', 'fecha', 'estado_pais', 'nombre_completo', 'puesto', 'area',
        'nivel_participacion', 'otro_participacion', 'nivel_influencia',
        'expectativa_personal', 'vision_12_meses', 'prioridades_estrategicas',
        'resolucion_actual', 'cambios_direccion', 'situaciones_freno', 'areas_criticas',
        'otra_area_critica', 'preocupacion_fallo', 'porque_iniciar_ahora',
        'espera_consultora', 'resultados_valiosos', 'problema_especifico',
        'orden_operaciones', 'orden_compras', 'orden_marketing', 'orden_rh',
        'orden_otro_nombre', 'orden_otro_valor', 'impacto', 'definicion_exito',
        'tiempo_resultados', 'no_negociable', 'nivel_orden_rh', 'alineacion_lideres_vision',
        'problemas_desempeno', 'problemas_liderazgo', 'procesos_no_documentados',
        'perfiles_reto', 'habitos_afectando_resultados', 'practicas_buenas',
        'perf_claridad_objetivos', 'perf_ejecucion_lideres', 'perf_accountability',
        'perf_calidad_talento', 'perf_alineacion_areas', 'resolucion_relevancia',
        'alcance_otro_texto', 'tambien_resolver', 'cambio_resultados_negocio',
        'temas_no_rh', 'depende_consultora', 'depende_empresa', 'depende_lideres',
        'impedimentos_logro', 'frecuencia_seguimiento', 'formato_avances',
        'indicadores_mejorar', 'resultado_6_meses', 'mejoras_cualitativas',
        'senal_mal_proyecto', 'frase_valga_la_pena', 'experiencia_reclutamiento', 'mejoras_proceso'
    ];
    protected $useTimestamps = false;
}