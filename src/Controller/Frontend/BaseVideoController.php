<?php

namespace App\Controller\Frontend;

use App\Form\VideoFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseVideoController extends AbstractController
{
    protected function getForm(Request $request): FormInterface
    {
        $form = $this->createForm(VideoFilterType::class);
        $form->handleRequest($request);
        return $form;
    }
}
