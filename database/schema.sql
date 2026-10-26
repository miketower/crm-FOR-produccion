-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 26, 2026 at 10:43 AM
-- Server version: 5.7.44-48
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `theblanc_webapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `alineacion_estrategica`
--

CREATE TABLE `alineacion_estrategica` (
  `id` int(11) NOT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado_pais` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombre_completo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `puesto` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nivel_participacion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `otro_participacion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nivel_influencia` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expectativa_personal` text COLLATE utf8_unicode_ci,
  `vision_12_meses` text COLLATE utf8_unicode_ci,
  `prioridades_estrategicas` text COLLATE utf8_unicode_ci,
  `resolucion_actual` text COLLATE utf8_unicode_ci,
  `cambios_direccion` text COLLATE utf8_unicode_ci,
  `situaciones_freno` text COLLATE utf8_unicode_ci,
  `areas_criticas` text COLLATE utf8_unicode_ci,
  `otra_area_critica` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `preocupacion_fallo` text COLLATE utf8_unicode_ci,
  `porque_iniciar_ahora` text COLLATE utf8_unicode_ci,
  `espera_consultora` text COLLATE utf8_unicode_ci,
  `resultados_valiosos` text COLLATE utf8_unicode_ci,
  `problema_especifico` text COLLATE utf8_unicode_ci,
  `orden_operaciones` int(11) DEFAULT NULL,
  `orden_compras` int(11) DEFAULT NULL,
  `orden_marketing` int(11) DEFAULT NULL,
  `orden_rh` int(11) DEFAULT NULL,
  `orden_otro_nombre` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orden_otro_valor` int(11) DEFAULT NULL,
  `impacto` text COLLATE utf8_unicode_ci,
  `definicion_exito` text COLLATE utf8_unicode_ci,
  `tiempo_resultados` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_negociable` text COLLATE utf8_unicode_ci,
  `nivel_orden_rh` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alineacion_lideres_vision` text COLLATE utf8_unicode_ci,
  `problemas_desempeno` text COLLATE utf8_unicode_ci,
  `problemas_liderazgo` text COLLATE utf8_unicode_ci,
  `procesos_no_documentados` text COLLATE utf8_unicode_ci,
  `perfiles_reto` text COLLATE utf8_unicode_ci,
  `habitos_afectando_resultados` text COLLATE utf8_unicode_ci,
  `practicas_buenas` text COLLATE utf8_unicode_ci,
  `perf_claridad_objetivos` int(11) DEFAULT NULL,
  `perf_ejecucion_lideres` int(11) DEFAULT NULL,
  `perf_accountability` int(11) DEFAULT NULL,
  `perf_calidad_talento` int(11) DEFAULT NULL,
  `perf_alineacion_areas` int(11) DEFAULT NULL,
  `resolucion_relevancia` text COLLATE utf8_unicode_ci,
  `alcance_otro_texto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tambien_resolver` text COLLATE utf8_unicode_ci,
  `cambio_resultados_negocio` text COLLATE utf8_unicode_ci,
  `temas_no_rh` text COLLATE utf8_unicode_ci,
  `depende_consultora` text COLLATE utf8_unicode_ci,
  `depende_empresa` text COLLATE utf8_unicode_ci,
  `depende_lideres` text COLLATE utf8_unicode_ci,
  `impedimentos_logro` text COLLATE utf8_unicode_ci,
  `frecuencia_seguimiento` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `formato_avances` text COLLATE utf8_unicode_ci,
  `indicadores_mejorar` text COLLATE utf8_unicode_ci,
  `resultado_6_meses` text COLLATE utf8_unicode_ci,
  `mejoras_cualitativas` text COLLATE utf8_unicode_ci,
  `senal_mal_proyecto` text COLLATE utf8_unicode_ci,
  `frase_valga_la_pena` text COLLATE utf8_unicode_ci,
  `experiencia_reclutamiento` text COLLATE utf8_unicode_ci,
  `mejoras_proceso` text COLLATE utf8_unicode_ci,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_qualitative`
--

CREATE TABLE `analysis_qualitative` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(10) UNSIGNED NOT NULL,
  `service_presentation_date` date DEFAULT NULL,
  `proposal_presentation_date` date DEFAULT NULL,
  `rejection_date` date DEFAULT NULL,
  `problem_to_solve` text,
  `trigger_event` text,
  `client_fear` text,
  `closing_accelerator` text,
  `real_brake` text,
  `services_sold` text,
  `services_rejected` text,
  `non_closing_main_factor` varchar(100) DEFAULT NULL,
  `non_closing_detail` varchar(255) DEFAULT NULL,
  `general_observations` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `capturas_comerciales`
--

CREATE TABLE `capturas_comerciales` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `canal` enum('Optimización','Soluciones','Otros') NOT NULL DEFAULT 'Optimización',
  `fecha` date NOT NULL,
  `citas_asociados` int(11) NOT NULL DEFAULT '0',
  `citas_directa` int(11) NOT NULL DEFAULT '0',
  `citas_eventos` int(11) NOT NULL DEFAULT '0',
  `citas_prosper` int(11) NOT NULL DEFAULT '0',
  `citas` int(11) NOT NULL DEFAULT '0',
  `propuestas` int(11) NOT NULL DEFAULT '0',
  `cierres` int(11) NOT NULL DEFAULT '0',
  `monto_vendido` decimal(15,2) NOT NULL DEFAULT '0.00',
  `monto_cobrado` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ingreso_nuevo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `observaciones` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categorias_macro`
--

CREATE TABLE `categorias_macro` (
  `id` int(11) NOT NULL,
  `nombre_categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cat_esquemas_dispersion`
--

CREATE TABLE `cat_esquemas_dispersion` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cat_esquemas_servicio`
--

CREATE TABLE `cat_esquemas_servicio` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cat_semaforos_socio`
--

CREATE TABLE `cat_semaforos_socio` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color_hex` varchar(20) DEFAULT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cat_tipos_socio`
--

CREATE TABLE `cat_tipos_socio` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cat_tipos_socios`
--

CREATE TABLE `cat_tipos_socios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `porcentaje_defecto` decimal(5,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cat_tipos_ventas`
--

CREATE TABLE `cat_tipos_ventas` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `citas_prospecto`
--

CREATE TABLE `citas_prospecto` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `giro` varchar(150) NOT NULL,
  `company_size` varchar(100) NOT NULL,
  `attendee_name` varchar(150) NOT NULL,
  `attendee_position` varchar(150) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `context` text NOT NULL,
  `internal_attendees` varchar(255) NOT NULL COMMENT 'Jorge Blanco, Alicia Gutierrez, etc.',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_externo` int(11) DEFAULT NULL,
  `socio_id` int(11) UNSIGNED DEFAULT NULL,
  `razon_social` varchar(255) NOT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cliente_esquemas`
--

CREATE TABLE `cliente_esquemas` (
  `cliente_id` int(11) UNSIGNED NOT NULL,
  `cat_esquema_servicio_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cortes_semanales`
--

CREATE TABLE `cortes_semanales` (
  `id` int(11) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `periodo` varchar(10) NOT NULL COMMENT 'Ej. 2026-07',
  `cat_esquema_servicio_id` int(11) UNSIGNED NOT NULL,
  `cat_tipo_venta_id` int(11) UNSIGNED NOT NULL,
  `facturacion` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ib_nuevo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ib_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `decision_profile`
--

CREATE TABLE `decision_profile` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(10) UNSIGNED NOT NULL,
  `first_contact_role` varchar(100) DEFAULT NULL,
  `presentation_role` varchar(100) DEFAULT NULL,
  `validator_role` varchar(100) DEFAULT NULL,
  `decision_maker_role` varchar(100) DEFAULT NULL,
  `psychological_profile` enum('Conservador','Estrategico','Otro') NOT NULL DEFAULT 'Otro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_especializado`
--

CREATE TABLE `detalle_especializado` (
  `id` int(11) NOT NULL,
  `propuesta_id` int(11) NOT NULL,
  `actual_bruto` decimal(12,2) DEFAULT '0.00',
  `actual_nomina` decimal(12,2) DEFAULT '0.00',
  `actual_bono` decimal(12,2) DEFAULT '0.00',
  `actual_carga` decimal(12,2) DEFAULT '0.00',
  `actual_otros` decimal(12,2) DEFAULT '0.00',
  `actual_total` decimal(12,2) DEFAULT '0.00',
  `base_100` decimal(12,2) DEFAULT '0.00',
  `neto_100` decimal(12,2) DEFAULT '0.00',
  `carga_100` decimal(12,2) DEFAULT '0.00',
  `sueldos_for` decimal(12,2) DEFAULT '0.00',
  `neto_for` decimal(12,2) DEFAULT '0.00',
  `excedente` decimal(12,2) DEFAULT '0.00',
  `carga_for` decimal(12,2) DEFAULT '0.00',
  `costo_op_100` decimal(12,2) DEFAULT '0.00',
  `comision_monto` decimal(12,2) DEFAULT '0.00',
  `total_for` decimal(12,2) DEFAULT '0.00',
  `diferencia` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_proem`
--

CREATE TABLE `detalle_proem` (
  `id` int(11) NOT NULL,
  `propuesta_id` int(11) NOT NULL,
  `actual_bruto` decimal(12,2) DEFAULT '0.00',
  `actual_nomina` decimal(12,2) DEFAULT '0.00',
  `actual_bono` decimal(12,2) DEFAULT '0.00',
  `actual_carga` decimal(12,2) DEFAULT '0.00',
  `actual_otros` decimal(12,2) DEFAULT '0.00',
  `actual_total` decimal(12,2) DEFAULT '0.00',
  `base_100` decimal(12,2) DEFAULT '0.00',
  `neto_100` decimal(12,2) DEFAULT '0.00',
  `carga_100` decimal(12,2) DEFAULT '0.00',
  `comision_100` decimal(12,2) DEFAULT '0.00',
  `comision_100_afecta` tinyint(1) DEFAULT '0',
  `bruto_proem` decimal(12,2) DEFAULT '0.00',
  `neto_proem` decimal(12,2) DEFAULT '0.00',
  `carga_proem` decimal(12,2) DEFAULT '0.00',
  `comision_empleado_proem` decimal(12,2) DEFAULT '0.00',
  `costo_op_100` decimal(12,2) DEFAULT '0.00',
  `excedente` decimal(12,2) DEFAULT '0.00',
  `comision_monto` decimal(12,2) DEFAULT '0.00',
  `total_proem` decimal(12,2) DEFAULT '0.00',
  `ahorro_mensual` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dg_auditoria`
--

CREATE TABLE `dg_auditoria` (
  `aud_id` int(11) NOT NULL,
  `dg_id_empresa` int(11) NOT NULL,
  `aud_organigrama` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_organigrama_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_frecuencia_org` enum('Mensual','Anual','Conforme a Cambio') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_plan_estrategico` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_plan_estrategico_otros` text COLLATE utf8mb4_unicode_ci,
  `aud_frecuencia_plan` enum('Anual','Trimestral','Otros') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_presupuesto_rh` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_kpis` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_kpis_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_tecnologia_tipo` enum('ERP','HCM','Ambos','Otros','Ninguno') COLLATE utf8mb4_unicode_ci DEFAULT 'Ninguno',
  `aud_tecnologia_cual` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_nom035` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_nom036` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_lft` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_imss` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_infonavit` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_stps` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_seguridad_salud` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_atraccion_seleccion` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_proceso_reclutamiento_std` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_perfiles_puesto_act` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_evaluaciones_psicometricas` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_employer_branding` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_onboarding_estructurado` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_plan_induccion` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_seguimiento_90_dias` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_plan_sucesion` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_puestos_criticos_id` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_desarrollo_liderazgo` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_evaluacion_desempeno` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_modelo_evaluacion` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_integracion_compensacion` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_compensacion_beneficios` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_estructura_salarial` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_valuacion_salarial` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_benchmark_mercado` enum('Sí','No','Parcial') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_beneficios_adicionales` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_beneficios_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_prestaciones_superiores` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_prestaciones_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_incentivos_variables` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_incentivos_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aud_politicas_documentadas` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_flexibilidad_laboral` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_valores_corp_def` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_medicion_clima` enum('Ocasional','Anual','Semestral','No se mide') COLLATE utf8mb4_unicode_ci DEFAULT 'No se mide',
  `aud_plan_talento_frec` enum('Anual','Mensual','Parcial','No tiene') COLLATE utf8mb4_unicode_ci DEFAULT 'No tiene',
  `aud_capacitacion_tecnica_frec` enum('Anual','Mensual','Parcial','No tiene') COLLATE utf8mb4_unicode_ci DEFAULT 'No tiene',
  `aud_soft_skills_frec` enum('Anual','Mensual','Parcial','No tiene') COLLATE utf8mb4_unicode_ci DEFAULT 'No tiene',
  `aud_desarrollo_lideres_frec` enum('Anual','Mensual','Parcial','No tiene') COLLATE utf8mb4_unicode_ci DEFAULT 'No tiene',
  `aud_certificaciones_frec` enum('Anual','Mensual','Parcial','No tiene') COLLATE utf8mb4_unicode_ci DEFAULT 'No tiene',
  `aud_programas_reconocimiento` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_reconocimientos_formales` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_programas_fidelizacion` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_gestion_cambio` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_comunicacion_interna` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_gestion_crisis` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_bienestar_seguridad` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_salud_fisica` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_bienestar_emocional` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_apoyo_psicologico` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_resiliencia_estres` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_anti_burnout` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_seguridad_higiene` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_comite_higiene` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_protocolos_riesgo` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_diversidad_inclusion` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_metricas_inclusion` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_formacion_sesgos` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_relacion_laboral_cumplimiento` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_sindicatos` enum('Sí','No','No Aplica') COLLATE utf8mb4_unicode_ci DEFAULT 'No Aplica',
  `aud_gestion_contratos` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_protocolo_quejas` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_manejo_conflictos` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_auditorias_internas` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_innovacion_futuro` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_digitalizacion_rh` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_people_analytics` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_ia_talento` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_sostenibilidad_rse` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_programas_comunitarios` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_impacto_social_talento` enum('Sí','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `aud_fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dg_empresas`
--

CREATE TABLE `dg_empresas` (
  `dg_id` int(11) NOT NULL,
  `dg_nombre_empresa` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dg_giro` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_actividad_preponderante` text COLLATE utf8_unicode_ci,
  `dg_nombre_gestor` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_cargo_gestor` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_total_colaboradores` int(11) NOT NULL,
  `dg_colab_administrativos` int(11) NOT NULL,
  `dg_colab_operativos` int(11) NOT NULL,
  `dg_tiene_rrhh` enum('si','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `dg_otros_cargos_rrhh` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dg_valuacion_salarial` enum('si','no') COLLATE utf8_unicode_ci DEFAULT 'no',
  `dg_define_rango_salarial` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_define_rango_otro` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_nomina_interna` enum('si','no') COLLATE utf8_unicode_ci DEFAULT 'si',
  `dg_nomina_ejecutor_externo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_tipo_reclutamiento` enum('Interno','Externo','Hibrido') COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_manejo_nomina` enum('Interno','Externo','Hibrido') COLLATE utf8_unicode_ci DEFAULT NULL,
  `dg_manejo_contable` enum('Interno','Externo') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dg_rrhh_puestos`
--

CREATE TABLE `dg_rrhh_puestos` (
  `rp_id` int(11) NOT NULL,
  `dg_id_empresa` int(11) DEFAULT NULL,
  `rp_puesto_nombre` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispersiones_comisiones`
--

CREATE TABLE `dispersiones_comisiones` (
  `id` int(11) UNSIGNED NOT NULL,
  `venta_flujo_id` int(11) UNSIGNED NOT NULL,
  `socio_id` int(11) UNSIGNED NOT NULL,
  `cat_esquema_dispersion_id` int(11) UNSIGNED NOT NULL,
  `monto_participacion` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `categoria_evento` enum('Externo','Interno') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Externo',
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `organizador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsable` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_evento` datetime DEFAULT NULL,
  `ubicacion` text COLLATE utf8_unicode_ci,
  `costo` decimal(10,2) DEFAULT '0.00',
  `presupuesto` decimal(10,2) DEFAULT '0.00',
  `liga_registro` text COLLATE utf8_unicode_ci,
  `objetivo` text COLLATE utf8_unicode_ci,
  `industria_segmentacion` text COLLATE utf8_unicode_ci,
  `roles_asistencia` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alcance` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipo_evento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invitados` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capacidad` int(11) DEFAULT '0',
  `tipo_asistente` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flyer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estatus` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'Pendiente',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventos_externos`
--

CREATE TABLE `eventos_externos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `organizador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_evento` datetime DEFAULT NULL,
  `ubicacion` text COLLATE utf8_unicode_ci,
  `costo` decimal(10,2) DEFAULT '0.00',
  `liga_registro` text COLLATE utf8_unicode_ci,
  `objetivo` text COLLATE utf8_unicode_ci,
  `industria_segmentacion` text COLLATE utf8_unicode_ci,
  `roles_asistencia` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alcance` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estatus` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'Pendiente',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventos_internos`
--

CREATE TABLE `eventos_internos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `responsable` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_evento` datetime DEFAULT NULL,
  `ubicacion` text COLLATE utf8_unicode_ci,
  `presupuesto` decimal(10,2) DEFAULT '0.00',
  `liga_registro` text COLLATE utf8_unicode_ci,
  `objetivo` text COLLATE utf8_unicode_ci,
  `tipo_evento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invitados` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capacidad` int(11) DEFAULT '0',
  `tipo_asistente` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `industria_dirigido` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flyer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estatus` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'Planeación',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_metrics`
--

CREATE TABLE `financial_metrics` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(10) UNSIGNED NOT NULL,
  `total_income_charged` decimal(15,2) NOT NULL DEFAULT '0.00',
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `commission_percent` decimal(5,2) DEFAULT NULL,
  `direct_costs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `indirect_costs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `real_margin_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `payroll_vs_income_percent` decimal(5,2) DEFAULT NULL,
  `rotation_percent` decimal(5,2) DEFAULT NULL,
  `yearly_dismissals_percent` decimal(5,2) DEFAULT NULL,
  `yearly_hiring_percent` decimal(5,2) DEFAULT NULL,
  `surplus_percent` decimal(5,2) DEFAULT NULL,
  `closing_cost_percent` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forecasts_anual`
--

CREATE TABLE `forecasts_anual` (
  `id` int(11) UNSIGNED NOT NULL,
  `anio` int(10) UNSIGNED NOT NULL,
  `meta_anual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forecasts_mensual`
--

CREATE TABLE `forecasts_mensual` (
  `id` int(11) UNSIGNED NOT NULL,
  `anio` int(10) UNSIGNED NOT NULL,
  `mes` tinyint(3) UNSIGNED NOT NULL,
  `citas` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `propuestas` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cierres` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forecasts_trimestral`
--

CREATE TABLE `forecasts_trimestral` (
  `id` int(11) UNSIGNED NOT NULL,
  `anio` int(10) UNSIGNED NOT NULL,
  `q` tinyint(3) UNSIGNED NOT NULL,
  `ingreso_bruto` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `giros`
--

CREATE TABLE `giros` (
  `id` int(11) NOT NULL,
  `nombre_giro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_benchmark`
--

CREATE TABLE `market_benchmark` (
  `id` int(11) NOT NULL,
  `propuesta_id` int(11) NOT NULL,
  `puesto` varchar(255) NOT NULL,
  `percentile_25` decimal(12,2) DEFAULT '0.00',
  `mean` decimal(12,2) DEFAULT '0.00',
  `median` decimal(12,2) DEFAULT '0.00',
  `percentile_75` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `perfiles_plantilla`
--

CREATE TABLE `perfiles_plantilla` (
  `id` int(11) NOT NULL,
  `propuesta_id` int(11) NOT NULL,
  `tipo_plan` varchar(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cargo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `per_nominas`
--

CREATE TABLE `per_nominas` (
  `per_id` int(11) NOT NULL,
  `dg_id_empresa` int(11) DEFAULT NULL,
  `per_puesto` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `per_periodo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `per_salario_base` decimal(10,2) DEFAULT NULL,
  `per_sd_integrado` decimal(10,2) DEFAULT NULL,
  `per_sueldo_neto` decimal(10,2) DEFAULT NULL,
  `per_horas_extras` decimal(10,2) DEFAULT NULL,
  `per_premio_puntualidad` decimal(10,2) DEFAULT NULL,
  `per_premio_asistencia` decimal(10,2) DEFAULT NULL,
  `per_prima_dominical` decimal(10,2) DEFAULT NULL,
  `per_pagos_fuera` decimal(10,2) DEFAULT NULL,
  `per_bonos_comisiones` decimal(10,2) DEFAULT NULL,
  `per_total_percepciones` decimal(10,2) DEFAULT NULL,
  `per_neto_pagado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pipeline_events`
--

CREATE TABLE `pipeline_events` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `stage` varchar(50) NOT NULL,
  `event_date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pipeline_history`
--

CREATE TABLE `pipeline_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `pipeline_stage` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `propuestas_for`
--

CREATE TABLE `propuestas_for` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `diag_360_inicial` tinyint(1) DEFAULT '0',
  `fecha_propuesta` date DEFAULT NULL,
  `solicitante` varchar(255) DEFAULT NULL,
  `cargo_solicitante` varchar(255) DEFAULT NULL,
  `impresion_diagnostica` text,
  `req_intervencion` varchar(20) DEFAULT NULL,
  `motivo_intervencion` text,
  `nivel_riesgo` int(11) DEFAULT '0',
  `plan_requerido` varchar(50) NOT NULL,
  `detalle_otros` varchar(255) DEFAULT NULL,
  `comision_pct` decimal(5,2) NOT NULL,
  `periodo_pago` varchar(20) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actual_bruto` decimal(12,2) DEFAULT '0.00',
  `actual_nomina` decimal(12,2) DEFAULT '0.00',
  `actual_excedente` decimal(12,2) DEFAULT '0.00',
  `actual_carga` decimal(12,2) DEFAULT '0.00',
  `actual_comision` decimal(12,2) DEFAULT '0.00',
  `actual_total` decimal(12,2) DEFAULT '0.00',
  `esp_base_100` decimal(12,2) DEFAULT '0.00',
  `esp_neto_100` decimal(12,2) DEFAULT '0.00',
  `esp_carga_100` decimal(12,2) DEFAULT '0.00',
  `esp_sueldos_for` decimal(12,2) DEFAULT '0.00',
  `esp_carga_for` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `prospects`
--

CREATE TABLE `prospects` (
  `id` int(11) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `pipeline_stage` varchar(50) NOT NULL DEFAULT 'Nuevo',
  `asistentes_cita` varchar(255) DEFAULT NULL,
  `company_name` varchar(150) NOT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `cat_tipo_venta_id` int(11) UNSIGNED DEFAULT NULL,
  `employees_count` int(11) NOT NULL,
  `location_state` varchar(50) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `classification_group` varchar(2) NOT NULL,
  `ticket_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `duration_months` int(11) NOT NULL DEFAULT '0',
  `complexity_level` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `last_stage_change_date` date DEFAULT NULL,
  `tipo_entidad` enum('Prospecto','Cliente') DEFAULT 'Prospecto',
  `origen_prospecto` varchar(150) DEFAULT NULL,
  `fecha_alta_formato` date DEFAULT NULL,
  `tipo_persona` enum('Física','Moral') DEFAULT 'Moral',
  `rfc` varchar(15) DEFAULT NULL,
  `actividad_principal` text,
  `domicilio_fiscal` text,
  `nombre_contacto` varchar(150) DEFAULT NULL,
  `telefono_contacto` varchar(50) DEFAULT NULL,
  `correo_electronico` varchar(150) DEFAULT NULL,
  `domicilio_envio_docs` text,
  `nombre_recibe_docs` varchar(150) DEFAULT NULL,
  `servicios_contratados` set('PROEM','ESPECIALIZADOS','PIC','SINDICATOS') DEFAULT NULL,
  `esquema_servicio_proem` text,
  `fecha_inicio_operacion` date DEFAULT NULL,
  `comision_base_cliente` text,
  `condiciones_iva` varchar(255) DEFAULT NULL,
  `monto_tentativo_operacion` decimal(15,2) DEFAULT '0.00',
  `personas_remunera` text,
  `profesion` varchar(150) DEFAULT NULL,
  `ingresos_otras_actividades` enum('Sí','No') DEFAULT 'No',
  `cuales_otras_actividades` text,
  `clabe_interbancaria` varchar(20) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `comisionista_id` int(11) UNSIGNED DEFAULT NULL,
  `pct_comisionista_asignado` decimal(5,2) DEFAULT '0.00',
  `red_socio_id` int(11) UNSIGNED DEFAULT NULL,
  `pct_red_asignado` decimal(5,2) DEFAULT '0.00',
  `comisionista_seguimiento_id` int(11) UNSIGNED DEFAULT NULL,
  `pct_seguimiento_asignado` decimal(5,2) DEFAULT '0.00' COMMENT 'Comisión una sola vez',
  `informacion_validada` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = Bloquea la edición de campos de comisiones para todos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `prospect_activities`
--

CREATE TABLE `prospect_activities` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `activity_type` enum('Llamada','Correo','Reunion','Nota','Tarea') NOT NULL DEFAULT 'Nota',
  `subject` varchar(255) NOT NULL,
  `notes` text,
  `due_date` datetime DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `prospect_documents`
--

CREATE TABLE `prospect_documents` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) NOT NULL COMMENT 'El nombre real del archivo (ej. Cotizacion_Final.pdf)',
  `category` varchar(50) DEFAULT 'Otros',
  `file_name` varchar(255) NOT NULL COMMENT 'El nombre encriptado en el servidor para evitar duplicados',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'Extensión del archivo',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `solicitante_nombre` varchar(150) NOT NULL,
  `solicitante_area` varchar(100) NOT NULL,
  `solicitante_correo` varchar(150) NOT NULL,
  `proyecto_nombre` varchar(150) NOT NULL,
  `tipo_proyecto` enum('sitio_web','formulario','app_movil','automatizacion','sistema_interno','otro') NOT NULL,
  `descripcion` text NOT NULL,
  `objetivo_negocio` text NOT NULL,
  `fecha_liberacion` date NOT NULL,
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `requiere_integracion` tinyint(1) DEFAULT '0',
  `detalles_integracion` text,
  `archivo_requerimiento` varchar(255) DEFAULT NULL,
  `presupuesto_estimado` varchar(100) DEFAULT NULL,
  `estatus` enum('pendiente','analisis','desarrollo','pruebas','liberado','cancelado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `registro_clinica_casos_exito`
--

CREATE TABLE `registro_clinica_casos_exito` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ocupacion` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `invitado_por` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `modalidad` enum('Presencial','Zoom') COLLATE utf8_unicode_ci NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sal_guias`
--

CREATE TABLE `sal_guias` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `editorial` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` int(11) NOT NULL,
  `pais` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo_fuente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sha256_pdf` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registros_esperados` int(11) NOT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sal_referencias`
--

CREATE TABLE `sal_referencias` (
  `id` int(10) UNSIGNED NOT NULL,
  `guia_id` int(10) UNSIGNED NOT NULL,
  `clave_fuente` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagina` int(11) NOT NULL,
  `area` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subarea` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `puesto` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `puesto_original` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contexto_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contexto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modalidad` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periodicidad` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimo` decimal(15,2) NOT NULL,
  `medio` decimal(15,2) DEFAULT NULL,
  `maximo` decimal(15,2) DEFAULT NULL,
  `operador_minimo` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variable_original` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variable_min` decimal(5,2) DEFAULT NULL,
  `variable_max` decimal(5,2) DEFAULT NULL,
  `facturacion` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nueva` smallint(6) NOT NULL DEFAULT '0',
  `demanda` smallint(6) NOT NULL DEFAULT '0',
  `estado` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `incidencias` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `texto_original` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fuente_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sal_referencia_zonas`
--

CREATE TABLE `sal_referencia_zonas` (
  `referencia_id` int(10) UNSIGNED NOT NULL,
  `zona` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seguimiento_socios`
--

CREATE TABLE `seguimiento_socios` (
  `id` int(11) UNSIGNED NOT NULL,
  `socio_id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED DEFAULT NULL,
  `semana_fecha` date NOT NULL,
  `citas_realizadas` int(11) NOT NULL DEFAULT '0',
  `citas_programadas` int(11) NOT NULL DEFAULT '0',
  `propuestas` int(11) NOT NULL DEFAULT '0',
  `cierres` int(11) NOT NULL DEFAULT '0',
  `asociados_nuevos` int(11) NOT NULL DEFAULT '0',
  `nombre_asociado_nuevo` varchar(150) DEFAULT NULL,
  `red_madre` varchar(150) DEFAULT NULL,
  `observaciones` text,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE `service_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pct_comisionista_base` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pct_red_base` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pct_seguimiento_base` decimal(5,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `socios`
--

CREATE TABLE `socios` (
  `id` int(11) UNSIGNED NOT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `cat_semaforo_id` int(11) UNSIGNED DEFAULT NULL,
  `cat_tipo_socio_id` int(11) UNSIGNED DEFAULT NULL,
  `porcentaje_personalizado` decimal(5,2) DEFAULT NULL COMMENT 'Si tiene valor, ignora el porcentaje por defecto del catálogo',
  `celular` varchar(20) DEFAULT NULL,
  `red_id` int(11) UNSIGNED DEFAULT NULL,
  `fecha_entrada` date DEFAULT NULL,
  `curp` varchar(20) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `estatus` varchar(50) DEFAULT 'Activo',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `estatus_convenio` varchar(50) DEFAULT 'Pendiente',
  `fecha_firma` date DEFAULT NULL,
  `ubicacion_fisica` varchar(255) DEFAULT NULL,
  `ubicacion_digital` varchar(255) DEFAULT NULL,
  `responsable_resguardo` varchar(150) DEFAULT NULL,
  `notas` text,
  `nivel_participacion` varchar(100) DEFAULT NULL COMMENT 'Ej: Activo, Interesada, Comisiona',
  `servicios_comisiona` varchar(255) DEFAULT 'No aplica' COMMENT 'Ej: No aplica, Optimización',
  `tipo_participacion` varchar(100) DEFAULT 'Asociado FOR' COMMENT 'Ej: Unidad Estratégica, Asociado FOR, Prosperhaus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `socios_rrrr`
--

CREATE TABLE `socios_rrrr` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets_operaciones`
--

CREATE TABLE `tickets_operaciones` (
  `id` int(11) UNSIGNED NOT NULL,
  `folio` varchar(20) NOT NULL,
  `area_departamento` varchar(100) NOT NULL,
  `creador_id` int(11) UNSIGNED NOT NULL,
  `que_sucedio` text NOT NULL,
  `fecha_suceso` date NOT NULL,
  `responsable_id` int(11) UNSIGNED DEFAULT NULL,
  `ejecutor_id` int(11) UNSIGNED DEFAULT NULL,
  `acciones_implementadas` text,
  `fecha_accion` date DEFAULT NULL,
  `que_se_logro` text,
  `tipo_accion` enum('politica','proceso','reglamento','iniciativa') DEFAULT NULL,
  `resultado` enum('proceso','logrado') NOT NULL DEFAULT 'proceso',
  `firma_supervisor_id` int(11) UNSIGNED DEFAULT NULL,
  `fecha_firma` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','manager','sales_rep','events_rep','commercial_rep','operaciones') NOT NULL DEFAULT 'sales_rep',
  `supervisor_id` int(11) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vacantes`
--

CREATE TABLE `vacantes` (
  `id` int(11) NOT NULL,
  `titulo_vacante` varchar(255) NOT NULL,
  `tipo` enum('Interna','Externa') DEFAULT 'Externa',
  `usuario_asignado` varchar(150) DEFAULT NULL,
  `empresa_cliente` varchar(255) NOT NULL,
  `fecha_apertura` date DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `posiciones` int(11) DEFAULT '1',
  `pipeline_stage` varchar(100) DEFAULT 'Vacante Abierta',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `estatus` enum('Activa','Pausada','Cerrada') DEFAULT 'Activa',
  `consultor` enum('Neto','Carlos') DEFAULT 'Neto',
  `etapa_num` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ventas_flujos`
--

CREATE TABLE `ventas_flujos` (
  `id` int(11) UNSIGNED NOT NULL,
  `prospect_id` int(11) UNSIGNED NOT NULL,
  `cat_esquema_servicio_id` int(11) UNSIGNED NOT NULL,
  `cat_tipo_venta_id` int(11) UNSIGNED DEFAULT NULL,
  `ingreso_bruto_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_validado_facturacion` decimal(15,2) NOT NULL DEFAULT '0.00',
  `fecha_vendido` date DEFAULT NULL,
  `fecha_cobrado` date DEFAULT NULL,
  `periodo` varchar(10) NOT NULL,
  `comision_bruta` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alineacion_estrategica`
--
ALTER TABLE `alineacion_estrategica`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `analysis_qualitative`
--
ALTER TABLE `analysis_qualitative`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analysis_qualitative_prospect_id_foreign` (`prospect_id`);

--
-- Indexes for table `capturas_comerciales`
--
ALTER TABLE `capturas_comerciales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_canal` (`canal`);

--
-- Indexes for table `categorias_macro`
--
ALTER TABLE `categorias_macro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indexes for table `cat_esquemas_dispersion`
--
ALTER TABLE `cat_esquemas_dispersion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_esquemas_servicio`
--
ALTER TABLE `cat_esquemas_servicio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_semaforos_socio`
--
ALTER TABLE `cat_semaforos_socio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_tipos_socio`
--
ALTER TABLE `cat_tipos_socio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_tipos_socios`
--
ALTER TABLE `cat_tipos_socios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cat_tipos_ventas`
--
ALTER TABLE `cat_tipos_ventas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `citas_prospecto`
--
ALTER TABLE `citas_prospecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_citas_prospect_id` (`prospect_id`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clientes_socio_id` (`socio_id`);

--
-- Indexes for table `cliente_esquemas`
--
ALTER TABLE `cliente_esquemas`
  ADD PRIMARY KEY (`cliente_id`,`cat_esquema_servicio_id`),
  ADD KEY `cliente_esquemas_cat_esquema_servicio_id_foreign` (`cat_esquema_servicio_id`);

--
-- Indexes for table `cortes_semanales`
--
ALTER TABLE `cortes_semanales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat_esquema_servicio_id` (`cat_esquema_servicio_id`),
  ADD KEY `cat_tipo_venta_id` (`cat_tipo_venta_id`);

--
-- Indexes for table `decision_profile`
--
ALTER TABLE `decision_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `decision_profile_prospect_id_foreign` (`prospect_id`);

--
-- Indexes for table `detalle_especializado`
--
ALTER TABLE `detalle_especializado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propuesta_id` (`propuesta_id`);

--
-- Indexes for table `detalle_proem`
--
ALTER TABLE `detalle_proem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propuesta_id` (`propuesta_id`);

--
-- Indexes for table `dg_auditoria`
--
ALTER TABLE `dg_auditoria`
  ADD PRIMARY KEY (`aud_id`),
  ADD KEY `fk_empresa_auditoria` (`dg_id_empresa`);

--
-- Indexes for table `dg_empresas`
--
ALTER TABLE `dg_empresas`
  ADD PRIMARY KEY (`dg_id`);

--
-- Indexes for table `dg_rrhh_puestos`
--
ALTER TABLE `dg_rrhh_puestos`
  ADD PRIMARY KEY (`rp_id`),
  ADD KEY `dg_id_empresa` (`dg_id_empresa`);

--
-- Indexes for table `dispersiones_comisiones`
--
ALTER TABLE `dispersiones_comisiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispersiones_comisiones_venta_flujo_id_foreign` (`venta_flujo_id`),
  ADD KEY `dispersiones_comisiones_socio_id_foreign` (`socio_id`),
  ADD KEY `dispersiones_comisiones_cat_esquema_dispersion_id_foreign` (`cat_esquema_dispersion_id`);

--
-- Indexes for table `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eventos_externos`
--
ALTER TABLE `eventos_externos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eventos_internos`
--
ALTER TABLE `eventos_internos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financial_metrics`
--
ALTER TABLE `financial_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_metrics_prospect_id_foreign` (`prospect_id`);

--
-- Indexes for table `forecasts_anual`
--
ALTER TABLE `forecasts_anual`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_anio` (`anio`);

--
-- Indexes for table `forecasts_mensual`
--
ALTER TABLE `forecasts_mensual`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_anio_mes` (`anio`,`mes`);

--
-- Indexes for table `forecasts_trimestral`
--
ALTER TABLE `forecasts_trimestral`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_anio_q` (`anio`,`q`);

--
-- Indexes for table `giros`
--
ALTER TABLE `giros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_giro` (`nombre_giro`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `market_benchmark`
--
ALTER TABLE `market_benchmark`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propuesta_id` (`propuesta_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfiles_plantilla`
--
ALTER TABLE `perfiles_plantilla`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propuesta_id` (`propuesta_id`);

--
-- Indexes for table `per_nominas`
--
ALTER TABLE `per_nominas`
  ADD PRIMARY KEY (`per_id`),
  ADD KEY `dg_id_empresa` (`dg_id_empresa`);

--
-- Indexes for table `pipeline_events`
--
ALTER TABLE `pipeline_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospect_id` (`prospect_id`);

--
-- Indexes for table `pipeline_history`
--
ALTER TABLE `pipeline_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospect_id` (`prospect_id`);

--
-- Indexes for table `propuestas_for`
--
ALTER TABLE `propuestas_for`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prospects`
--
ALTER TABLE `prospects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospects_created_by_foreign` (`created_by`),
  ADD KEY `prospects_assigned_to_foreign` (`assigned_to`),
  ADD KEY `fk_prospects_comisionista` (`comisionista_id`),
  ADD KEY `fk_prospects_red_socio` (`red_socio_id`),
  ADD KEY `fk_prospects_comisionista_seg` (`comisionista_seguimiento_id`),
  ADD KEY `fk_prospects_tipo_venta` (`cat_tipo_venta_id`);

--
-- Indexes for table `prospect_activities`
--
ALTER TABLE `prospect_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospect_id` (`prospect_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prospect_documents`
--
ALTER TABLE `prospect_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prospect_id` (`prospect_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registro_clinica_casos_exito`
--
ALTER TABLE `registro_clinica_casos_exito`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sal_guias`
--
ALTER TABLE `sal_guias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sal_guias_codigo` (`codigo`);

--
-- Indexes for table `sal_referencias`
--
ALTER TABLE `sal_referencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sal_referencias_fuente` (`guia_id`,`clave_fuente`),
  ADD KEY `ix_sal_referencias_area` (`guia_id`,`area`),
  ADD KEY `ix_sal_referencias_estado` (`estado`);

--
-- Indexes for table `sal_referencia_zonas`
--
ALTER TABLE `sal_referencia_zonas`
  ADD PRIMARY KEY (`referencia_id`,`zona`),
  ADD KEY `ix_sal_referencia_zonas_zona` (`zona`);

--
-- Indexes for table `seguimiento_socios`
--
ALTER TABLE `seguimiento_socios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_seg_socio_id` (`socio_id`),
  ADD KEY `fk_seg_prospect_id` (`prospect_id`);

--
-- Indexes for table `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_socios_semaforo` (`cat_semaforo_id`),
  ADD KEY `fk_socios_tipo` (`cat_tipo_socio_id`),
  ADD KEY `fk_socios_red` (`red_id`);

--
-- Indexes for table `socios_rrrr`
--
ALTER TABLE `socios_rrrr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_supervisor` (`supervisor_id`);

--
-- Indexes for table `vacantes`
--
ALTER TABLE `vacantes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ventas_flujos`
--
ALTER TABLE `ventas_flujos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_flujos_cat_esquema_servicio_id_foreign` (`cat_esquema_servicio_id`),
  ADD KEY `ventas_flujos_cat_tipo_venta_id_foreign` (`cat_tipo_venta_id`),
  ADD KEY `fk_ventas_prospecto` (`prospect_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alineacion_estrategica`
--
ALTER TABLE `alineacion_estrategica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analysis_qualitative`
--
ALTER TABLE `analysis_qualitative`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `capturas_comerciales`
--
ALTER TABLE `capturas_comerciales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorias_macro`
--
ALTER TABLE `categorias_macro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_esquemas_dispersion`
--
ALTER TABLE `cat_esquemas_dispersion`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_esquemas_servicio`
--
ALTER TABLE `cat_esquemas_servicio`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_semaforos_socio`
--
ALTER TABLE `cat_semaforos_socio`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_tipos_socio`
--
ALTER TABLE `cat_tipos_socio`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_tipos_socios`
--
ALTER TABLE `cat_tipos_socios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cat_tipos_ventas`
--
ALTER TABLE `cat_tipos_ventas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `citas_prospecto`
--
ALTER TABLE `citas_prospecto`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cortes_semanales`
--
ALTER TABLE `cortes_semanales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `decision_profile`
--
ALTER TABLE `decision_profile`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_especializado`
--
ALTER TABLE `detalle_especializado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_proem`
--
ALTER TABLE `detalle_proem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dg_auditoria`
--
ALTER TABLE `dg_auditoria`
  MODIFY `aud_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dg_empresas`
--
ALTER TABLE `dg_empresas`
  MODIFY `dg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dg_rrhh_puestos`
--
ALTER TABLE `dg_rrhh_puestos`
  MODIFY `rp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispersiones_comisiones`
--
ALTER TABLE `dispersiones_comisiones`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventos_externos`
--
ALTER TABLE `eventos_externos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventos_internos`
--
ALTER TABLE `eventos_internos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_metrics`
--
ALTER TABLE `financial_metrics`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forecasts_anual`
--
ALTER TABLE `forecasts_anual`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forecasts_mensual`
--
ALTER TABLE `forecasts_mensual`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forecasts_trimestral`
--
ALTER TABLE `forecasts_trimestral`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `giros`
--
ALTER TABLE `giros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `market_benchmark`
--
ALTER TABLE `market_benchmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perfiles_plantilla`
--
ALTER TABLE `perfiles_plantilla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `per_nominas`
--
ALTER TABLE `per_nominas`
  MODIFY `per_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pipeline_events`
--
ALTER TABLE `pipeline_events`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pipeline_history`
--
ALTER TABLE `pipeline_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propuestas_for`
--
ALTER TABLE `propuestas_for`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prospects`
--
ALTER TABLE `prospects`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prospect_activities`
--
ALTER TABLE `prospect_activities`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prospect_documents`
--
ALTER TABLE `prospect_documents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registro_clinica_casos_exito`
--
ALTER TABLE `registro_clinica_casos_exito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sal_guias`
--
ALTER TABLE `sal_guias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sal_referencias`
--
ALTER TABLE `sal_referencias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seguimiento_socios`
--
ALTER TABLE `seguimiento_socios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `socios`
--
ALTER TABLE `socios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `socios_rrrr`
--
ALTER TABLE `socios_rrrr`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vacantes`
--
ALTER TABLE `vacantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ventas_flujos`
--
ALTER TABLE `ventas_flujos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analysis_qualitative`
--
ALTER TABLE `analysis_qualitative`
  ADD CONSTRAINT `analysis_qualitative_prospect_id_foreign` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `citas_prospecto`
--
ALTER TABLE `citas_prospecto`
  ADD CONSTRAINT `fk_citas_prospect_id` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_socio_id` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cliente_esquemas`
--
ALTER TABLE `cliente_esquemas`
  ADD CONSTRAINT `cliente_esquemas_cat_esquema_servicio_id_foreign` FOREIGN KEY (`cat_esquema_servicio_id`) REFERENCES `cat_esquemas_servicio` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cliente_esquemas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cortes_semanales`
--
ALTER TABLE `cortes_semanales`
  ADD CONSTRAINT `cortes_semanales_ibfk_1` FOREIGN KEY (`cat_esquema_servicio_id`) REFERENCES `cat_esquemas_servicio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cortes_semanales_ibfk_2` FOREIGN KEY (`cat_tipo_venta_id`) REFERENCES `cat_tipos_ventas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `decision_profile`
--
ALTER TABLE `decision_profile`
  ADD CONSTRAINT `decision_profile_prospect_id_foreign` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detalle_especializado`
--
ALTER TABLE `detalle_especializado`
  ADD CONSTRAINT `detalle_especializado_ibfk_1` FOREIGN KEY (`propuesta_id`) REFERENCES `propuestas_for` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detalle_proem`
--
ALTER TABLE `detalle_proem`
  ADD CONSTRAINT `detalle_proem_ibfk_1` FOREIGN KEY (`propuesta_id`) REFERENCES `propuestas_for` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dg_auditoria`
--
ALTER TABLE `dg_auditoria`
  ADD CONSTRAINT `fk_empresa_auditoria` FOREIGN KEY (`dg_id_empresa`) REFERENCES `dg_empresas` (`dg_id`) ON DELETE CASCADE;

--
-- Constraints for table `dg_rrhh_puestos`
--
ALTER TABLE `dg_rrhh_puestos`
  ADD CONSTRAINT `dg_rrhh_puestos_ibfk_1` FOREIGN KEY (`dg_id_empresa`) REFERENCES `dg_empresas` (`dg_id`) ON DELETE CASCADE;

--
-- Constraints for table `dispersiones_comisiones`
--
ALTER TABLE `dispersiones_comisiones`
  ADD CONSTRAINT `dispersiones_comisiones_cat_esquema_dispersion_id_foreign` FOREIGN KEY (`cat_esquema_dispersion_id`) REFERENCES `cat_esquemas_dispersion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispersiones_comisiones_socio_id_foreign` FOREIGN KEY (`socio_id`) REFERENCES `socios_rrrr` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_metrics`
--
ALTER TABLE `financial_metrics`
  ADD CONSTRAINT `financial_metrics_prospect_id_foreign` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `forecasts_mensual`
--
ALTER TABLE `forecasts_mensual`
  ADD CONSTRAINT `fk_forecast_mes_anio` FOREIGN KEY (`anio`) REFERENCES `forecasts_anual` (`anio`) ON DELETE CASCADE;

--
-- Constraints for table `forecasts_trimestral`
--
ALTER TABLE `forecasts_trimestral`
  ADD CONSTRAINT `fk_forecast_trim_anio` FOREIGN KEY (`anio`) REFERENCES `forecasts_anual` (`anio`) ON DELETE CASCADE;

--
-- Constraints for table `giros`
--
ALTER TABLE `giros`
  ADD CONSTRAINT `giros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_macro` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `market_benchmark`
--
ALTER TABLE `market_benchmark`
  ADD CONSTRAINT `market_benchmark_ibfk_1` FOREIGN KEY (`propuesta_id`) REFERENCES `propuestas_for` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perfiles_plantilla`
--
ALTER TABLE `perfiles_plantilla`
  ADD CONSTRAINT `perfiles_plantilla_ibfk_1` FOREIGN KEY (`propuesta_id`) REFERENCES `propuestas_for` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `per_nominas`
--
ALTER TABLE `per_nominas`
  ADD CONSTRAINT `per_nominas_ibfk_1` FOREIGN KEY (`dg_id_empresa`) REFERENCES `dg_empresas` (`dg_id`);

--
-- Constraints for table `prospects`
--
ALTER TABLE `prospects`
  ADD CONSTRAINT `fk_prospects_comisionista` FOREIGN KEY (`comisionista_id`) REFERENCES `socios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prospects_comisionista_seg` FOREIGN KEY (`comisionista_seguimiento_id`) REFERENCES `socios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prospects_red_socio` FOREIGN KEY (`red_socio_id`) REFERENCES `socios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prospects_tipo_venta` FOREIGN KEY (`cat_tipo_venta_id`) REFERENCES `cat_tipos_ventas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prospects_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prospects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `prospect_activities`
--
ALTER TABLE `prospect_activities`
  ADD CONSTRAINT `fk_activity_prospect` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prospect_documents`
--
ALTER TABLE `prospect_documents`
  ADD CONSTRAINT `fk_doc_prospect` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sal_referencias`
--
ALTER TABLE `sal_referencias`
  ADD CONSTRAINT `sal_referencias_ibfk_1` FOREIGN KEY (`guia_id`) REFERENCES `sal_guias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sal_referencia_zonas`
--
ALTER TABLE `sal_referencia_zonas`
  ADD CONSTRAINT `sal_referencia_zonas_ibfk_1` FOREIGN KEY (`referencia_id`) REFERENCES `sal_referencias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seguimiento_socios`
--
ALTER TABLE `seguimiento_socios`
  ADD CONSTRAINT `fk_seg_prospect_id` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_seg_socio_id` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `fk_socios_red` FOREIGN KEY (`red_id`) REFERENCES `socios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_socios_semaforo` FOREIGN KEY (`cat_semaforo_id`) REFERENCES `cat_semaforos_socio` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_socios_tipo` FOREIGN KEY (`cat_tipo_socio_id`) REFERENCES `cat_tipos_socios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ventas_flujos`
--
ALTER TABLE `ventas_flujos`
  ADD CONSTRAINT `fk_ventas_prospecto` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_flujos_cat_esquema_servicio_id_foreign` FOREIGN KEY (`cat_esquema_servicio_id`) REFERENCES `cat_esquemas_servicio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_flujos_cat_tipo_venta_id_foreign` FOREIGN KEY (`cat_tipo_venta_id`) REFERENCES `cat_tipos_ventas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
