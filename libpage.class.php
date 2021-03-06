<?php

require_once 'configure.php';
require_once 'libpiratewww.class.php';

interface Piratepage {
        public function type();
}

class Page implements Piratepage {
	private $type;
	private $subs;
	private $moresubs;
	private $html;
	protected $settings;

	public $id;
        public $title;
	public $content;
	public $template;

	function __construct() {
		global $settings;
		$this->settings=$settings;
	}

	// 
	private function loadFile($f) {
		return file_get_contents($f);
	}
	
	// 
	private function loadInclude($t){
		return $this->loadFile($this->settings['BASEDIR'].$this->settings['INCLUDES'].$t);
	}
	
	// 
	public function addSub($tag, $re){
		$this->moresubs['tag'][] = $tag;
		$this->moresubs['re'][] = $re;
	}
	
	private function loadMoreSubs(){
	    if(is_array($this->moresubs)){
            foreach($this->moresubs['tag'] as $m){
                $this->subs['tag'][]=$m;
            }
            foreach($this->moresubs['re'] as $m){
                $this->subs['re'][]=$m;
            }
		}
	}

	private function loadSubs() {
		// all pages
		$tag[] = '<!--include:ppheader-->';
		$re[] = $this->loadInclude('ppheader.inc.html');
		$tag[] = '<!--include:sitenav-->';
		$re[] = $this->loadInclude('sitenav.inc.html');
		$tag[] = '<!--include:ppfooter-->';
		$re[] = $this->loadInclude('ppfooter.inc.html');
		
		// Templating
		$tag[]='<!--include:textgoeshere-->';
		$re[]=$this->content;
		$tag[]='<!--templating:id-->';
		$re[]=$this->id;
		$tag[]='<!--templating:title-->';
		$re[]=$this->title;
		$tag[]='<!--templating:fancytitle-->';
		$re[]=$this->title;
		
		// Redmine's html validation.
		$tag[] = '<a name=';
		$re[] = '<a id=';
		$tag[] = '<br /><br />';
		$re[] = '</p><p>';
		//$tag[] = '<br />';
		//$re[] = ' '; //si che le fa in ordine... :D
		

		$this->subs['tag'] = $tag;
		$this->subs['re'] = $re;
		
		//senza di questa non funzionano gli indexintro!
		$this->loadMoreSubs(); 
	}
	
	private function make(){
		$this->loadSubs();
		$this->html = $this->loadFile($this->settings['BASEDIR'].$this->settings['TEMPLATES'].$this->template);
		return $this->html = str_replace($this->subs['tag'], $this->subs['re'], $this->html);
	}

	function type() {
	    return $this->type;
        }

	function writePage(){
		$this->make();
		return file_put_contents($this->settings['BASEDIR'].$this->settings['HTDOCS'].$this->id.'.html', $this->html);
	}
};

class Index extends Page {
	private $pages;
	private $chunksno;

	public $excerptlen=3000;
	public $intro;
	
	function __construct($template) {
		parent::__construct();
		
		$this->type = $template;
		$this->template= $template.'.html';
	}

	private function writeIndex() {
		// è inutile. si poteva chiamare direttamente.
		parent::writePage(); 
	}

	private function chunking() {
		$this->chunksno = 0;
		$chunks = array_chunk($this->pages, $this->settings['INDEXPAGE'], true);
		$this->chunksno = count($chunks);
		$this->chunksno--;
		return $chunks;
	}


	private function elementsToHtml($pages) {
		foreach ( $pages as $page ) {
			// $page come oggetto Page::Liquidpage? Si, ok.
			// $page->source contiene l'initiative, si possono usare i suoi pezzi per comporre l'indice.
			$this->content .= "\n".'<dt id='.$page->source['id'].'><a href="'.$page->id.'.html">'.$page->title.': '.$page->source['name'].'</a></dt>'."\n";
			$this->content .= '<dd>'."\n";
			$this->content .= 'Iniziativa n. '.$page->source['initiative_id'].' - Area n. '.$page->source['area_id'].' ('.$page->source['area_name'].')'."<br>\n";
			$this->content .= 'ID: '.hash('sha256', $page->source['created'].$page->source['id'].$page->source['name'].$page->source['content'])."\n";
			$this->content .= "<p><small>Pubblicato in Gazzetta Ufficiale dall'Assemblea Permanente,<br> li' <time datetime=".$page->source['created'].">".$page->source['created'].".</time></small></p>\n";
			$this->content .= '</dd>'."\n";
		}
	}

	function addElement($page) {
		$this->pages[] = $page;
	}

	function createIndex() {
		$chunks = $this->chunking();
		$indexchunk = 0;
		$this->type = $this->id;
		foreach($chunks as $chunk) {
			$this->content = '<dl>';
			$this->elementsToHtml($chunk);
			$this->content .= '</dl>'."\n";
			if ( $this->chunksno > 0 ) {
				if ( $indexchunk > 0 ) $this->id = $this->type.'_i'.$indexchunk;
				$previd = $indexchunk - 1;
				$nextid = $indexchunk + 1;
				$this->content .= '<div align="center">'; //ovvove.
				if ( $indexchunk != 0 ) {
					if ( $indexchunk > 1 ) {
						$prevlink = $this->type."_i".$previd.'.html';
					} else {
						$prevlink = $this->type.'.html';
					}
					$this->content .= '<a href="'.$prevlink.'">';
					$this->content .= 'Successive';
					$this->content .= '</a>';
				}
				if ( $indexchunk != 0 && $indexchunk != $this->chunksno ) {
					$this->content .= ' | ';
				}
				if ( $indexchunk != $this->chunksno ) {
					$this->content .= '<a href="'.$this->type."_i".$nextid.'.html'.'">';
					$this->content .= 'Precedenti';
					$this->content .= '</a>';
				}
				$this->content .= '</div>';
			}
			$this->writeIndex($indexchunk);
			$indexchunk++;
		}
	}
}

//
class Formalfoo extends Page {
	function __construct() {
		parent::__construct();

                $this->type="formalfoo";
		$this->template= 'wikipages.html'; //solo il nome del file dentro la certella dei templates.
	}
	//dubito che dovremmo mettere qualcosa qui.
}

//
class Liquidpage extends Page {
	public $source;

	// roba che dovrebbero condividere Report e Tribune?
	function __construct($source, $type) {
		parent::__construct();
		
		$this->type = $type;
		$this->source = $source;

                if ($this->settings['DEBUG']) echo "chiamando $type\n";

                switch($type) {
                    case "report":
                        $this->id='verbale_'.$source['id'];
                        $this->template='report.html';
                        $this->title = 'Proposta n. '.$source['id'];
                    break;
                    case "tribune":
                        $this->id='tribuna_'.$source['issue_id'].'_'.$source['initiative_id'];
                        $this->template='tribune.html';
                        $this->title = 'Tema n. '.$source['issue_id'];
                    break;
                }

		file_put_contents($this->settings['BASEDIR'].'rocketwiki.temp', $this->source['content']);
                $comando="./rocketwiki < ".$this->settings['BASEDIR'].'rocketwiki.temp';
                $uscita="";
                $ritorno="1";
                exec($comando,$uscita,$ritorno);
                $source['content'] = $uscita;

                $this->content .= "<article id=init".$this->id.">";
                $this->content .= "<hgroup><h6>Area n. ".$source['area_id']." &#x2283; Tema n. ".$source['issue_id']." &#x2283; Iniziativa n.".$source['initiative_id']." &#x220B; Proposta n.".$source['id']."</h6>";
                $this->content .= "<h1>".$source['name']."</h1>";
                $this->content .= "<h6>ID: ".hash('sha256', $source['created'].$source['id'].$source['name'].$source['content'])."</h6></hgroup>\n";
                $this->content .= "<p>".$source['content']."</p>";
                $this->content .= "<footer><p>Pubblicato in Gazzetta Ufficiale dall'Assemblea Permanente,<br> li' <time datetime=".$source['created'].">".$source['created'].".</time></p>";
                $comments = $this->addComments($source['id']);
                if ( $this->type == "tribune" ) {
                    $this->content .= '<ul class="comments">'."\n";
                    foreach ( $comments as $comment ) {
                        $this->content .= '<li>Commento: <a href="'.$comment.'">'.$comment.'</a></li>'."\n";
                    }
                $this->content .= '</ul>'."\n";
                }
                $this->content .= "</footer>\n";
                $this->content .= "</article>\n";
	}

        private function addComments($draftid) {
                $comments[0] = "http://blog.partitopirata.org/2012/12/30/post-name";
                $comments[1] = "http://www.ilfattoquotidiano.it/2012/03/19/il-successo-di-costruire-la-normalita/198683/";
                $comments[2] = "http://www.beppegrillo.it/2012/03/passaparola_viv/index.html";
                $comments[3] = "http://dentroefuoricasapound.wordpress.com/2012/01/23/intervista-sul-libro-mediapolitika/";
                return $comments;
        }

}

?>
