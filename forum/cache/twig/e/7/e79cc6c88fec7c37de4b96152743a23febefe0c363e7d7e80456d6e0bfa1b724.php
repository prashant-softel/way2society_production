<?php

/* ucp_register.html */
class __TwigTemplate_e79cc6c88fec7c37de4b96152743a23febefe0c363e7d7e80456d6e0bfa1b724 extends Twig_Template
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
        $value = "register";
        $context['definition']->set('NAV_SECTION', $value);
        $location = "overall_header.html";
        $namespace = false;
        if (strpos($location, '@') === 0) {
            $namespace = substr($location, 1, strpos($location, '/') - 1);
            $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
            $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
        }
        $this->loadTemplate("overall_header.html", "ucp_register.html", 1)->display($context);
        if ($namespace) {
            $this->env->setNamespaceLookUpOrder($previous_look_up_order);
        }
        // line 2
        echo "
<script type=\"text/javascript\">
// <![CDATA[
\t/**
\t* Change language
\t*/
\tfunction change_language(lang_iso)
\t{
\t\tdocument.cookie = '";
        // line 10
        echo (isset($context["COOKIE_NAME"]) ? $context["COOKIE_NAME"] : null);
        echo "_lang=' + lang_iso + '; path=";
        echo (isset($context["COOKIE_PATH"]) ? $context["COOKIE_PATH"] : null);
        echo "';
\t\tdocument.forms['register'].change_lang.value = lang_iso;
\t\tdocument.forms['register'].submit.click();
\t}
// ]]>
</script>

<form id=\"register\" method=\"post\" action=\"";
        // line 17
        echo (isset($context["S_UCP_ACTION"]) ? $context["S_UCP_ACTION"] : null);
        echo "\"";
        echo (isset($context["S_FORM_ENCTYPE"]) ? $context["S_FORM_ENCTYPE"] : null);
        echo ">

<div class=\"panel\">
\t<div class=\"inner\">

\t<h2>";
        // line 22
        echo (isset($context["SITENAME"]) ? $context["SITENAME"] : null);
        echo " - ";
        echo $this->env->getExtension('phpbb')->lang("REGISTRATION");
        echo "</h2>

\t<fieldset class=\"fields2\">
\t";
        // line 25
        if ((isset($context["ERROR"]) ? $context["ERROR"] : null)) {
            echo "<dl><dd class=\"error\">";
            echo (isset($context["ERROR"]) ? $context["ERROR"] : null);
            echo "</dd></dl>";
        }
        // line 26
        echo "\t";
        if ((isset($context["L_REG_COND"]) ? $context["L_REG_COND"] : null)) {
            // line 27
            echo "\t\t<dl><dd><strong>";
            echo $this->env->getExtension('phpbb')->lang("REG_COND");
            echo "</strong></dd></dl>
\t";
        }
        // line 29
        echo "\t";
        // line 30
        echo "\t<dl>
\t\t<dt><label for=\"username\">";
        // line 31
        echo $this->env->getExtension('phpbb')->lang("USERNAME");
        echo $this->env->getExtension('phpbb')->lang("COLON");
        echo "</label><br /><span>";
        echo $this->env->getExtension('phpbb')->lang("USERNAME_EXPLAIN");
        echo "</span></dt>
\t\t<dd><input type=\"text\" tabindex=\"1\" name=\"username\" id=\"username\" size=\"25\" value=\"";
        // line 32
        echo (isset($context["USERNAME"]) ? $context["USERNAME"] : null);
        echo "\" class=\"inputbox autowidth\" title=\"";
        echo $this->env->getExtension('phpbb')->lang("USERNAME");
        echo "\" /></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"email\">";
        // line 35
        echo $this->env->getExtension('phpbb')->lang("EMAIL_ADDRESS");
        echo $this->env->getExtension('phpbb')->lang("COLON");
        echo "</label></dt>
\t\t<dd><input type=\"email\" tabindex=\"2\" name=\"email\" id=\"email\" size=\"25\" maxlength=\"100\" value=\"";
        // line 36
        echo (isset($context["EMAIL"]) ? $context["EMAIL"] : null);
        echo "\" class=\"inputbox autowidth\" title=\"";
        echo $this->env->getExtension('phpbb')->lang("EMAIL_ADDRESS");
        echo "\" autocomplete=\"off\" /></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"new_password\">";
        // line 39
        echo $this->env->getExtension('phpbb')->lang("PASSWORD");
        echo $this->env->getExtension('phpbb')->lang("COLON");
        echo "</label><br /><span>";
        echo $this->env->getExtension('phpbb')->lang("PASSWORD_EXPLAIN");
        echo "</span></dt>
\t\t<dd><input type=\"password\" tabindex=\"4\" name=\"new_password\" id=\"new_password\" size=\"25\" value=\"";
        // line 40
        echo (isset($context["PASSWORD"]) ? $context["PASSWORD"] : null);
        echo "\" class=\"inputbox autowidth\" title=\"";
        echo $this->env->getExtension('phpbb')->lang("NEW_PASSWORD");
        echo "\" autocomplete=\"off\" /></dd>
\t</dl>
\t<dl>
\t\t<dt><label for=\"password_confirm\">";
        // line 43
        echo $this->env->getExtension('phpbb')->lang("CONFIRM_PASSWORD");
        echo $this->env->getExtension('phpbb')->lang("COLON");
        echo "</label></dt>
\t\t<dd><input type=\"password\"  tabindex=\"5\" name=\"password_confirm\" id=\"password_confirm\" size=\"25\" value=\"";
        // line 44
        echo (isset($context["PASSWORD_CONFIRM"]) ? $context["PASSWORD_CONFIRM"] : null);
        echo "\" class=\"inputbox autowidth\" title=\"";
        echo $this->env->getExtension('phpbb')->lang("CONFIRM_PASSWORD");
        echo "\" autocomplete=\"off\" /></dd>
\t</dl>

\t";
        // line 47
        // line 48
        echo "\t<hr />

\t";
        // line 50
        // line 51
        echo "\t<dl>
\t\t<dt><label for=\"lang\">";
        // line 52
        echo $this->env->getExtension('phpbb')->lang("LANGUAGE");
        echo $this->env->getExtension('phpbb')->lang("COLON");
        echo "</label></dt>
\t\t<dd><select name=\"lang\" id=\"lang\" onchange=\"change_language(this.value); return false;\" tabindex=\"6\" title=\"";
        // line 53
        echo $this->env->getExtension('phpbb')->lang("LANGUAGE");
        echo "\">";
        echo (isset($context["S_LANG_OPTIONS"]) ? $context["S_LANG_OPTIONS"] : null);
        echo "</select></dd>
\t</dl>

\t";
        // line 56
        $location = "timezone_option.html";
        $namespace = false;
        if (strpos($location, '@') === 0) {
            $namespace = substr($location, 1, strpos($location, '/') - 1);
            $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
            $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
        }
        $this->loadTemplate("timezone_option.html", "ucp_register.html", 56)->display($context);
        if ($namespace) {
            $this->env->setNamespaceLookUpOrder($previous_look_up_order);
        }
        // line 57
        echo "
\t";
        // line 58
        // line 59
        echo "\t";
        if (twig_length_filter($this->env, $this->getAttribute((isset($context["loops"]) ? $context["loops"] : null), "profile_fields", array()))) {
            // line 60
            echo "\t\t<dl><dd><strong>";
            echo $this->env->getExtension('phpbb')->lang("ITEMS_REQUIRED");
            echo "</strong></dd></dl>

\t";
            // line 62
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["loops"]) ? $context["loops"] : null), "profile_fields", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["profile_fields"]) {
                // line 63
                echo "\t\t<dl>
\t\t\t<dt><label";
                // line 64
                if ($this->getAttribute($context["profile_fields"], "FIELD_ID", array())) {
                    echo " for=\"";
                    echo $this->getAttribute($context["profile_fields"], "FIELD_ID", array());
                    echo "\"";
                }
                echo ">";
                echo $this->getAttribute($context["profile_fields"], "LANG_NAME", array());
                echo $this->env->getExtension('phpbb')->lang("COLON");
                if ($this->getAttribute($context["profile_fields"], "S_REQUIRED", array())) {
                    echo " *";
                }
                echo "</label>
\t\t\t";
                // line 65
                if ($this->getAttribute($context["profile_fields"], "LANG_EXPLAIN", array())) {
                    echo "<br /><span>";
                    echo $this->getAttribute($context["profile_fields"], "LANG_EXPLAIN", array());
                    echo "</span>";
                }
                // line 66
                echo "\t\t\t";
                if ($this->getAttribute($context["profile_fields"], "ERROR", array())) {
                    echo "<br /><span class=\"error\">";
                    echo $this->getAttribute($context["profile_fields"], "ERROR", array());
                    echo "</span>";
                }
                echo "</dt>
\t\t\t<dd>";
                // line 67
                echo $this->getAttribute($context["profile_fields"], "FIELD", array());
                echo "</dd>
\t\t</dl>
\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['profile_fields'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 70
            echo "\t";
        }
        // line 71
        echo "
\t";
        // line 72
        // line 73
        echo "\t</fieldset>
\t</div>
</div>
";
        // line 76
        if ((isset($context["CAPTCHA_TEMPLATE"]) ? $context["CAPTCHA_TEMPLATE"] : null)) {
            // line 77
            echo "\t";
            $value = 8;
            $context['definition']->set('CAPTCHA_TAB_INDEX', $value);
            // line 78
            echo "\t";
            $location = (("" . (isset($context["CAPTCHA_TEMPLATE"]) ? $context["CAPTCHA_TEMPLATE"] : null)) . "");
            $namespace = false;
            if (strpos($location, '@') === 0) {
                $namespace = substr($location, 1, strpos($location, '/') - 1);
                $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
                $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
            }
            $this->loadTemplate((("" . (isset($context["CAPTCHA_TEMPLATE"]) ? $context["CAPTCHA_TEMPLATE"] : null)) . ""), "ucp_register.html", 78)->display($context);
            if ($namespace) {
                $this->env->setNamespaceLookUpOrder($previous_look_up_order);
            }
        }
        // line 80
        echo "
";
        // line 81
        if ((isset($context["S_COPPA"]) ? $context["S_COPPA"] : null)) {
            // line 82
            echo "

<div class=\"panel\">
\t<div class=\"inner\">

\t<h4>";
            // line 87
            echo $this->env->getExtension('phpbb')->lang("COPPA_COMPLIANCE");
            echo "</h4>

\t<p>";
            // line 89
            echo $this->env->getExtension('phpbb')->lang("COPPA_EXPLAIN");
            echo "</p>
\t</div>
</div>
";
        }
        // line 93
        echo "
<div class=\"panel\">
\t<div class=\"inner\">

\t<fieldset class=\"submit-buttons\">
\t\t";
        // line 98
        echo (isset($context["S_HIDDEN_FIELDS"]) ? $context["S_HIDDEN_FIELDS"] : null);
        echo "
\t\t<input type=\"reset\" value=\"";
        // line 99
        echo $this->env->getExtension('phpbb')->lang("RESET");
        echo "\" name=\"reset\" class=\"button2\" />&nbsp;
\t\t<input type=\"submit\" tabindex=\"9\" name=\"submit\" id=\"submit\" value=\"";
        // line 100
        echo $this->env->getExtension('phpbb')->lang("SUBMIT");
        echo "\" class=\"button1 default-submit-action\" />
\t\t";
        // line 101
        echo (isset($context["S_FORM_TOKEN"]) ? $context["S_FORM_TOKEN"] : null);
        echo "
\t</fieldset>

\t</div>
</div>
</form>

";
        // line 108
        $location = "overall_footer.html";
        $namespace = false;
        if (strpos($location, '@') === 0) {
            $namespace = substr($location, 1, strpos($location, '/') - 1);
            $previous_look_up_order = $this->env->getNamespaceLookUpOrder();
            $this->env->setNamespaceLookUpOrder(array($namespace, '__main__'));
        }
        $this->loadTemplate("overall_footer.html", "ucp_register.html", 108)->display($context);
        if ($namespace) {
            $this->env->setNamespaceLookUpOrder($previous_look_up_order);
        }
    }

    public function getTemplateName()
    {
        return "ucp_register.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  326 => 108,  316 => 101,  312 => 100,  308 => 99,  304 => 98,  297 => 93,  290 => 89,  285 => 87,  278 => 82,  276 => 81,  273 => 80,  259 => 78,  255 => 77,  253 => 76,  248 => 73,  247 => 72,  244 => 71,  241 => 70,  232 => 67,  223 => 66,  217 => 65,  203 => 64,  200 => 63,  196 => 62,  190 => 60,  187 => 59,  186 => 58,  183 => 57,  171 => 56,  163 => 53,  158 => 52,  155 => 51,  154 => 50,  150 => 48,  149 => 47,  141 => 44,  136 => 43,  128 => 40,  121 => 39,  113 => 36,  108 => 35,  100 => 32,  93 => 31,  90 => 30,  88 => 29,  82 => 27,  79 => 26,  73 => 25,  65 => 22,  55 => 17,  43 => 10,  33 => 2,  19 => 1,);
    }
}
