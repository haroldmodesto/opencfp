<?php
namespace OpenCFP\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use OpenCFP\Model\Talk;
use OpenCFP\Model\Speaker;

class DashboardController
{
    public function indexAction(Request $req, Application $app)
    {
        if (!$app['sentry']->check()) {
            return $app->redirect($app['url'] . '/login');
        }

        $user = $app['sentry']->getUser();
        $user_mapper = $app['spot']->mapper('OpenCFP\Entity\User');
        $user_info = $user_mapper->get($user->getId())->toArray();

        $speaker_data = $user_mapper->getDetails($user->getId());;

        $talk_mapper = $app['spot']->mapper('OpenCFP\Entity\Talk');
        $my_talks = $talk_mapper->getByUser($user->getId());

        $permissions['admin'] = $user->hasPermission('admin');

        // Load our template and RENDER
        $template = $app['twig']->loadTemplate('dashboard.twig');
        $template_data = array(
            'myTalks' => $my_talks,
            'user' => $user_info,
            'first_name' => $speaker_data['first_name'],
            'last_name' => $speaker_data['last_name'],
            'company' => $speaker_data['company'] ?: null,
            'url' => $speaker_data['url'],
            'twitter' => $speaker_data['twitter'],
            'speaker_info' => $speaker_data['info'],
            'speaker_bio' => $speaker_data['bio'],
            'transportation' => $speaker_data['transportation'],
            'hotel' => $speaker_data['hotel'],
            'speaker_photo' => $speaker_data['photo_path'],
            'preview_photo' => $app['uploadPath'] . $speaker_data['photo_path'],
            'airport' => $speaker_data['airport'],
            'permissions' => $permissions,
            'current_page' => '/dashboard'
        );

        return $template->render($template_data);
    }

    public function ideasAction(Request $req, Application $app)
    {
    	// Load our template and RENDER
    	$template = $app['twig']->loadTemplate('ideas.twig');
    	$template_data = array();

    	return $template->render($template_data);
    }

    public function packageAction(Request $req, Application $app)
    {
    	// Load our template and RENDER
    	$template = $app['twig']->loadTemplate('package.twig');
    	$template_data = array();

    	return $template->render($template_data);
    }
}

