<?php

/* index.twig */
class __TwigTemplate_7be49c7ffa703aa15f8650719354e35b54d0f276b78eb1d0551a7864e2355cb8 extends Twig_Template
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
        return new Twig_Source("{{name}}", "index.twig", "/var/www/local.safe-drive.org/public_html/App/Views/index.twig");
    }
}
