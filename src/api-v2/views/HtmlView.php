<?php

/**
 * HTML View class: renders HTML 5
 *
 * @category View
 * @package  API
 * @author   Lorna Mitchel <lorna.mitchell@gmail.com>
 * @author   Rob Allen <rob@akrabat.com>
 * @license  BSD see doc/LICENSE
 */
class HtmlView extends ApiView
{
    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function render($content)
    {
        $content = $this->addCount($content);

        header('Content-Type: text/html; charset=utf8');
        $this->layoutStart();
        $this->printArray($content);
        $this->layoutStop();
        return true;
    }

    /**
     * Recursively render an array to an HTML list
     *
     * @param array $content data to be rendered
     *
     * @return null
     */
    protected function printArray($content)
    {
        echo "<ul>\n";

        // field name
        foreach ($content as $field => $value) {
            echo "<li><strong>" . $field . ":</strong> ";
            if (is_array($value)) {
                // recurse
                $this->printArray($value);
            } else {
                // value, with hyperlinked hyperlinks
                $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
                if (strpos($value, 'http://') === 0) {
                    echo "<a href=\"" . $value . "\">" . $value . "</a>";
                } else {
                    echo $value;
                }
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    }

    /**
     * Render start of HTML page
     *
     * @return null
     */
    protected function layoutStart()
    {
        echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>API v2</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
    body {
        margin: 10px;
    }
    #content {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 15px;
        color: #fff;
        
        background: #4A98D6; /* for non-css3 browsers */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#024184', endColorstr='#4A98D6'); /* for IE */
        background: -webkit-gradient(linear, left top, left bottom, from(#024184), to(#4A98D6)); /* for webkit browsers */
        background: -moz-linear-gradient(top, #024184,  #4A98D6); /* for firefox 3.6+ */ 
        
        border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
        
        padding: 5px;
    }

    ul {
        padding-bottom: 15px;
        padding-left: 20px;
    } 
    a {
        color: #fff /*#FF950C*/;
    }
    </style>
</head>
<body>
<div id="content">
EOT;
    }

    /**
     * Render end of HTML page
     *
     * @return null
     */
    protected function layoutStop()
    {
        echo <<<EOT
</div>
</body>
</html>

EOT;
    }
}
