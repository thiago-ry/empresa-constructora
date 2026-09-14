<?php

require_once __DIR__ . "/../modelos/LandingPage.php";

class LandingPageController
{
    private $landingPage;

    public function __construct()
    {
        $this->landingPage = new LandingPage();
    }

    public function obtenerTodos()
    {
        return $this->landingPage->obtenerTodos();
    }
}