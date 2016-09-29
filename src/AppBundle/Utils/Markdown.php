<?php

namespace AppBundle\Utils;

function terr($err) {
    
    echo "\n   ------------\n   |".$err."|\n   ------------\n";
}

define('_N', "\n");

class Markdown
{
    private $parser;
    private $lastOrderedListNumber = 0;

    public function __construct()
    {
        $this->parser = new \Parsedown();
    }

    public function toHtml($text)
    {
        $html = $this->parser->text($text);

        return $html;
    }
    
    //
    public function up2down($markup) {
        
        $markup = explode("\n", $markup);
        $return = '';
        $block = '';
        
        foreach ($markup as $lineNo => $line) {
            
            $lastLine = isset($markup[$lineNo - 1]) ? $markup[$lineNo - 1] : false;
            $nextLine = isset($markup[$lineNo + 1]) ? $markup[$lineNo + 1] : false;
            
            //block start
            if (preg_match('!\{\|(.*)!')) {

                //insert blank line before block
                $block .= trim($lastLine) != _N  ? _N : '';
                
                $block .= $line . "\n";
                continue;
            }  
            
            //block line
            if ($block != '') {

                $block .= $line . "\n";
                continue;                
            }
            
            //block end
            if (preg_match('!\|\}!')) {
                
                $block .= $line . "\n";
                
                //recursiv parsing
                $return .= $this->up2down($this->parseTable($block));
                
                //stop block parsing
                $block = '';
                continue;
            } 
            
            $return .= $this->parseLine($line) . "\n";
        }
        
        return $return;
    }
    
    //
    private function parseLine($line) {
        
        //template: tag
        $line = preg_replace('/({{)([^\|]*)(\|)([^\|]*)(}})/', '{{$2=$4}}', $line);
        
        //unordered list | *eins -> * eins
        $line = preg_replace("/(\*)([^\s])(.*)/", "$1 $2$3", $line);
        
        //Code
        $line = str_replace("<code>", '`', $line);
        $line = str_replace("</code>", '`', $line);
        
        //Var
        $line = str_replace("<var>", '', $line);
        $line = str_replace("</var>", '', $line);
        
        //bold | ''' -> **
        $line = str_replace("'''", '**', $line);
        
        //italic | '' -> *
        $line = str_replace("''", '*', $line);
        
        //linebreak | <br> -> _n_
        $line = str_replace("<br>", '_n_', $line);
        
        
        //Domain for links
        $domain = 'https://wiki.openstreetmap.org/wiki';
        
        //Links | [] and [[]] -> [title](link)
        $pattern = '~\[(?:\[([^]]+)]|([^] ]+) ([^]]+))]~';   
        $line = preg_replace_callback($pattern, 
            function ($m) use ($domain) {
            
                //external Link
                if (empty($m[1])) {
                    
                    return sprintf('[%s](%s)', $m[3], str_replace(' ', '_', $m[2]));
                }
            
                //Image
                if (substr($m[1], 0, 6) == 'image:') {

                    preg_match('!Image:([^\|]*)\|(.*)\|(.*)\|(.*)!i', $m[1], $image);
                    
                    $url = str_replace(' ', '_', $image[1]);
                    $linkname = empty($image[4]) ? $image[1] : $image[4];
                    
                    return sprintf('[Bild->%s](%s/File:%s)', $linkname, $domain, $url);
                }
            
                //Internal Link
                if (preg_match('!([^|]*)\|?(.*)!', $m[1], $link)) {
                    
                    $url = str_replace(' ', '_', $link[1]);
                    $linkname = empty($link[2]) ? $link[1] : $link[2];
                 
                    return sprintf('[%s](%s/%s)', $linkname, $domain, $url);
                }
            }, 
        $line);
        
        //ordered list | # -> 1., 2.....
        if (preg_match('!(#) (.*)!', $line, $parts)) {
            
            $number = ++$this->lastOrderedListNumber;
            $content = trim($parts[2]);            
            $line = $number . '. ' . $content;
        }        
        else {
            
            $this->lastOrderedListNumber = 0;
        }
        
        //heading | == Var == -> ## Var
        if (preg_match('!^(=+)([^=]{0,})(=+)!', $line, $parts)) {
            
            $length = strlen($parts[1]);
            $content = trim($parts[2]);
            
            $line = str_repeat('#', $length).' '.$content;
        }
        
        return $line;
    }
    
    
    private function parseBlock($block) {
        
        //table
        if (preg_match('!^{|!', $block)) {
            
            return $this->parseTable($block);
        }
        
        foreach ($block as $line) {
            
            
        }
    }

    private function parseTable($table) {
        
        $table = explode("\n", $table);
        unset($table[0]);
        unset($table[count($table)-1]);
        
        $row = 0;
        $th = [];
        $tr = [];
        foreach ($table as $lineNo => $line) {
            
            //th
            if (substr($line,0,1) == '!') {
                
                //there might mutiple headcells in one line
                $ths = array_map('trim', explode('!!', substr($line, 1)));
                 
                //add cells to row
                $th = array_merge($th, $ths);
                
                continue;
            }
            
            //tr
            if (substr($line,0,2) == '|-') {
                
                $row++;
                
                continue;
            }
            
            //td
            if (substr($line,0,1) == '|') {
                
                //td is || or |
                $cut = substr($line,0,2) == '||' ? 2 : 1;
                
                //there might multiple cells in one line
                $tds = array_map('trim', explode('||', substr($line, $cut)));
                 
                //add cells to row
                empty($tr[$row]) AND $tr[$row] = [];
                $tr[$row] = array_merge($tr[$row], $tds);
                
                continue;
            }
        }
        
        $return = '';
        
        //
        if (!empty($th)) {
            
            $return .= '| ' . implode(' | ', $th) . ' |' . _N;
            $return .= '|';
            
            for ($i = 0; $i < count($th); $i++) {
                
                $return .= ' -------- |';
            }
            
            $return .= _N;
        }
        
        foreach ($tr as $tds) {
            
            $return .= '| ' . implode(' | ', $tds) . ' |' . _N;
        }
        
        return $return;
    }
}

