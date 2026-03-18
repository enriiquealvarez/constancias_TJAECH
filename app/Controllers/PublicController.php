<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Models\Certificate;

class PublicController extends Controller
{
    public function home()
    {
        $this->render('public/home', [
            'title' => 'Inicio',
        ], 'public');
    }

    public function verifyForm()
    {
        $this->render('public/verify', [
            'title' => 'Verificar',
        ], 'public');
    }

    public function show($token)
    {
        $record = Certificate::findByToken($token);
        if (!$record) {
            $this->render('public/not_found', [
                'title' => 'Constancia no encontrada',
                'token' => $token,
            ], 'public');
            return;
        }
        $this->render('public/certificate', [
            'title' => 'Constancia verificada',
            'record' => $record,
        ], 'public');
    }

    public function notFound()
    {
        $this->render('public/not_found', [
            'title' => 'No encontrado',
        ], 'public');
    }
}
