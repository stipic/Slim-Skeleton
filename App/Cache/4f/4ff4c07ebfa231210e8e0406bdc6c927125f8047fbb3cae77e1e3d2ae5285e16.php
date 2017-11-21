<?php

/* index.twig */
class __TwigTemplate_2726f497c5fcb87b382e3aebc6aa8a21a7c96c9ed9661c4e2e6fd596584914b1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.twig", "/var/www/local.safe-drive.org/public_html/App/Views/index.twig");
    }
}
