<?php
namespace App\Controllers;
use App\Models\AlineacionModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Formularios extends BaseController {
    
    public function alineacion() {
        return view('formularios/alineacion_estrategica');
    }

    public function guardarAlineacion() {
        $model = new AlineacionModel();
        
        // Procesar checkboxes de 'areas_criticas' e 'impacto' como en el original
        $areas = $this->request->getPost('areas_criticas');
        $impacto = $this->request->getPost('impacto');
        $resolucion = $this->request->getPost('resolucion_relevancia');

        $data = $this->request->getPost();
        $data['areas_criticas'] = $areas ? implode(', ', $areas) : '';
        $data['impacto'] = $impacto ? implode(', ', $impacto) : '';
        $data['resolucion_relevancia'] = $resolucion ? implode(', ', $resolucion) : '';

        if ($model->insert($data)) {
            return redirect()->to('/formularios/alineacion')->with('success', 'Formulario guardado con éxito.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al guardar los datos.');
        }
    }

    public function descargarPdf($id) {
        $model = new AlineacionModel();
        $data['registro'] = $model->find($id);

        if (!$data['registro']) {
            return redirect()->to('/formularios/listado')->with('error', 'Registro no encontrado.');
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Para cargar el logo
        $dompdf = new Dompdf($options);

        // Renderizar la vista especial para PDF
        $html = view('formularios/pdf_alineacion', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nombre del archivo: Empresa_Nombre_Fecha.pdf
        $filename = "Alineacion_" . $data['registro']['empresa'] . "_" . date('Ymd') . ".pdf";
        
        return $this->response->setHeader('Content-Type', 'application/pdf')
                             ->setBody($dompdf->output())
                             ->download($filename, null);
    }
}