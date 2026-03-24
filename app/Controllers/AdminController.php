<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Core\Auth;
use app\Core\Csrf;

class AdminController extends Controller
{
    public function dashboard()
    {
        Auth::requireAuth();
        $this->render('admin/dashboard', [
            'title' => 'Dashboard',
            'csrf' => Csrf::token(),
        ], 'admin');
    }

    public function courses()
    {
        Auth::requireAuth();
        if (!Auth::can('manage_courses') && !Auth::can('view_courses')) {
            $this->render('admin/forbidden', ['title' => 'Acceso restringido'], 'admin');
            return;
        }

        // Fetch courses from Evaluaciones database
        $pdo = \app\Core\Database::connection();
        $evalCourses = [];
        try {
            $stmt = $pdo->query("SELECT nombre FROM tjaechgob_tjaech_eval.cursos WHERE activo = 1 ORDER BY id DESC");
            $evalCourses = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Throwable $e) {
            error_log('Error fetching eval courses: ' . $e->getMessage());
        }

        $this->render('admin/courses', [
            'title' => 'Cursos',
            'csrf' => Csrf::token(),
            'can_manage' => Auth::can('manage_courses'),
            'eval_courses' => $evalCourses
        ], 'admin');
    }

    public function participants()
    {
        Auth::requireAuth();
        if (!Auth::can('manage_participants') && !Auth::can('view_participants')) {
            $this->render('admin/forbidden', ['title' => 'Acceso restringido'], 'admin');
            return;
        }
        $this->render('admin/participants', [
            'title' => 'Participantes',
            'csrf' => Csrf::token(),
            'can_manage' => Auth::can('manage_participants'),
        ], 'admin');
    }

    public function certificates()
    {
        Auth::requireAuth();
        if (!Auth::can('manage_certificates') && !Auth::can('view_certificates')) {
            $this->render('admin/forbidden', ['title' => 'Acceso restringido'], 'admin');
            return;
        }
        $this->render('admin/certificates', [
            'title' => 'Constancias',
            'csrf' => Csrf::token(),
            'can_manage' => Auth::can('manage_certificates'),
        ], 'admin');
    }

    public function audit()
    {
        Auth::requireAuth();
        if (!Auth::can('view_audit')) {
            $this->render('admin/forbidden', ['title' => 'Acceso restringido'], 'admin');
            return;
        }
        $this->render('admin/audit', [
            'title' => 'Auditoria',
            'csrf' => Csrf::token(),
        ], 'admin');
    }

    public function users()
    {
        Auth::requireAuth();
        if (!Auth::can('manage_users')) {
            $this->render('admin/forbidden', ['title' => 'Acceso restringido'], 'admin');
            return;
        }
        $this->render('admin/users', [
            'title' => 'Usuarios',
            'csrf' => Csrf::token(),
        ], 'admin');
    }
}
