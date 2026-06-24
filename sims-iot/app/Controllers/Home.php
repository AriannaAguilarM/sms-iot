<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to('/dashboard');
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard - SueñoSmart IoT',
            'page_title' => 'Dashboard',
            'page_sub' => 'Monitor de sueño en tiempo real'
        ];
        return view('dashboard/index', $data);
    }

    public function historial()
    {
        $data = [
            'title' => 'Historial - SueñoSmart IoT',
            'page_title' => 'Historial',
            'page_sub' => 'Registro completo de lecturas'
        ];
        return view('historial/index', $data);
    }

    public function estadisticas()
    {
        $data = [
            'title' => 'Estadísticas - SueñoSmart IoT',
            'page_title' => 'Estadísticas',
            'page_sub' => 'Análisis detallado de tu sueño'
        ];
        return view('estadisticas/index', $data);
    }

    public function alertas()
    {
        $data = [
            'title' => 'Alertas - SueñoSmart IoT',
            'page_title' => 'Alertas',
            'page_sub' => 'Notificaciones y recomendaciones'
        ];
        return view('alertas/index', $data);
    }

    public function configuracion()
    {
        $data = [
            'title' => 'Configuración - SueñoSmart IoT',
            'page_title' => 'Configuración',
            'page_sub' => 'Ajustes del sistema'
        ];
        return view('configuracion/index', $data);
    }

    public function usuarios()
    {
        $data = [
            'title' => 'Usuarios - SueñoSmart IoT',
            'page_title' => 'Usuarios',
            'page_sub' => 'Gestión de usuarios del sistema'
        ];
        return view('usuarios/index', $data);
    }

    public function acerca()
    {
        $data = [
            'title' => 'Acerca de - SueñoSmart IoT',
            'page_title' => 'Acerca de',
            'page_sub' => 'Información del proyecto',
            'repo_url' => 'https://github.com/AriannaAguilarM/sms-iot',
            'repo_name' => 'sueno-smart-iot',
            'repo_user' => 'tu-usuario',
            'author' => 'Arianna Aguilar Martínez',
            'institution' => 'Tecnológico Nacional de México - ITS Mulegé',
            'year' => date('Y')
        ];
        return view('acerca/index', $data);
    }

}