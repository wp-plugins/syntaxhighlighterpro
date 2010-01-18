<?php
/*
 Plugin Name: SyntaxHighlighterPro
 Plugin URI: http://www.blogcube.org/
 Version: 1.0.0
 Author: <a href="http://www.blogcube.org">Joe</a>
 Description: SyntaxHighlighterPro uses the <a href="http://alexgorbatchev.com/wiki/SyntaxHighlighter">SyntaxHighlighter</a> to highlight code snippets. Major features:  1.Supports C++, C#, CSS, Delphi, Java, JavaScript, PHP, Python, Ruby, SQL, VB, XML, and HTML.   2.Support up to 5 theme styles.  3.Allow you to choose what languages will support, which is great to avoid unecessary javascript imports.

 */
?>
<?php
if(!class_exists('GoogleSyntaxHighlighterPro')){
	class GoogleSyntaxHighlighterPro{
		//(label,language,default)
		var $defaultLanguages=array('AS3'=>array('ActionScript3','false'),
		'Bash'=>array('Bash','true'),
		'ColdFusion'=>array('ColdFusion','false'),
		'Cpp'=>array('C++','true'),
		'CSharp'=>array('C#','true'),
		'Css'=>array('Css','true'),
		'Delphi'=>array('Delphi','false'),
		'Diff'=>array('Diff','false'),
		'Erlang'=>array('Erlang','false'),
		'Groovy'=>array('Groovy','true'),
		'Java'=>array('Java','true'),
		'JavaFX'=>array('JavaFX','false'),
		'JScript'=>array('JavaScript','true'),
		'Perl'=>array('Perl','true'),
		'Php'=>array('Php','true'),
		'Plain'=>array('Plain','true'),
		'PowerShell'=>array('PowerShell','false'),
		'Python'=>array('Python','false'),
		'Ruby'=>array('Ruby','true'),
		'Scala'=>array('Scala','false'),
		'Sql'=>array('SQL','true'),
		'Vb'=>array('VB','false'),
		'Xml'=>array('XML','true'));

		var $themes=array('Default','Django','Eclipse','Emacs','FadeToGrey','Midnight','RDark');


		var $adminOptionsName = 'SyntaxHighlighterAdminOptions';
		var $googleSyntaxHighlighterVersion='2.1.364';

		//load styles and scripts
		function loadResources() {
			wp_enqueue_style('shCore.css', plugins_url('/styles/shCore.css',__FILE__), false,$this->googleSyntaxHighlighterVersion , 'all');
			
			wp_enqueue_script('shCore.js',plugins_url('/scripts/shCore.js',__FILE__),false,$this->googleSyntaxHighlighterVersion,true);
			$scriptsOption=$this->adminOptions();	
			foreach($scriptsOption as $lan=>$option){
				if(array_key_exists($lan,$this->defaultLanguages)){
					$lanEnabled=$option[1];
					if($lanEnabled=='true'){
						wp_enqueue_script('shBrush'.$lan,plugins_url('/scripts/shBrush'.$lan.'.js',__FILE__),false,$this->googleSyntaxHighlighterVersion,true);
					}
				}
				
				if($lan=='theme'){
					wp_enqueue_style('shTheme'.$option.'.css', plugins_url('/styles/shTheme'.$option.'.css',__FILE__), false,$this->googleSyntaxHighlighterVersion , 'all');
				}
			}


		}
		//render the scripts
		function runscripts(){
			$scriptsLoader='<script type="text/javascript">'
			.'SyntaxHighlighter.config.clipboardSwf = "'.plugins_url('/scripts/clipboard.swf',__FILE__).'";'
			.'SyntaxHighlighter.all();'
			.'</script>';
			echo($scriptsLoader);

		}


		function init(){
			$this->adminOptions();
		}
		function adminOptions(){

			$adminOptions=$this->defaultLanguages;
			$devOptions=get_option($this->adminOptionsName);
			if(!empty($devOptions)){
				foreach($devOptions as $lan=>$option){
					if(array_key_exists($lan,$adminOptions)){
						$adminOptions[$lan][1]=$option[1];
					}
				}
				if(array_key_exists('theme',$devOptions)&&$devOptions['theme']){
					$adminOptions['theme']=$devOptions['theme'];
				}else{
					$adminOptions['theme']='Default';
				}
			}else{
				$adminOptions['theme']='Default';
			}

			update_option($this->adminOptionsName,$adminOptions);
				
			return $adminOptions;
		}


		function SyntaxHighlighterPrintAdminPage(){
			$devOptions = $this->adminOptions();
			if(isset($_POST['Update_Google_Syntax_Highlighter_Plugin_Settings'])){
				foreach($this->defaultLanguages as $lanValue=>$lan){
					$lanLabel=$lan[0];
					$lanEnabled=$lan[1];
					if(isset($_POST[$lanValue])){
						$devOptions[$lanValue][1]=$_POST[$lanValue];
					}
				}
				$devOptions['theme']=$_POST['theme'];
				update_option($this->adminOptionsName,$devOptions);
				?>
<div class="updated">
<p><strong><?php _e("Settings Updated.","syntaxhighlighter");?></strong></p>
</div>
				<?php
			}//end of update
			?>
<div class="wrap">
<form method="post" action="<?php echo($_SERVER['REQUEST_URI']);?>
">

<h2>Google Code Syntax Highlighter Settings</h2>

<h3>Theme styles</h3>
<p><?php
foreach($this->themes as $theme){
	?> <label for="Theme<?php echo($theme);?>"> <input type="radio"
	id="Theme<?php echo($theme);?>" name="theme"
	value="<?php echo($theme);?>"
	<?php
	if($devOptions['theme']==$theme){
		_e('checked="checked"','SyntaxHighlighterPro'); }?> /><?php echo($theme);?></label>
		<?php
}//end of foreach themes
?></p>


<h3>Load scripts for the following languages:</h3>
<?php

foreach($devOptions as $lanValue=>$lan){
	if($lanValue=='theme'){
	 continue;
	}
	$lanLabel=$lan[0];
	$lanEnabled=$lan[1];
	
	?>

<fieldset>
<p><strong><?php echo($lanLabel);?></strong> <label
	for="<?php echo($lanValue); ?>_Yes"> <input type="radio"
	id="<?php echo($lanValue); ?>_Yes" name="<?php echo($lanValue); ?>"
	value="true"
	<?php

	if($lanEnabled=='true'||($lanEnabled==true)){
		_e('checked="checked"','SyntaxHighlighterPro'); }?> />Yes</label> <label
	for="<?php echo($lanValue); ?>_No"><input type="radio"
	id="<?php echo($lanValue); ?>_No" name="<?php echo($lanValue); ?>"
	value="false"
	<?php
	if($lanEnabled=='false'||($lanEnabled==false)){
		_e('checked="checked"','SyntaxHighlighterPro'); }?> />No</label></p>
</fieldset>

		<?php }//end of foreach for defaultLanguages?>
<div class="submit"><input type="submit"
	name="Update_Google_Syntax_Highlighter_Plugin_Settings"
	value="<?php _e('Update Settings', 'SyntaxHighlighterPro') ?>" /></div>
</form>

</div>

		<?php

		}//end of SyntaxHighlighterPrintAdminPage function
	}// end of class
}// end of class define

//Class initialize
$googleSyntaxHighlighter=NULL;
if(class_exists('GoogleSyntaxHighlighterPro')){
	$googleSyntaxHighlighter=new GoogleSyntaxHighlighterPro();
}

if(!function_exists('SyntaxHighlighterAdminPanel')){
	function SyntaxHighlighterAdminPanel(){
		global $googleSyntaxHighlighter;
		if(!isset($googleSyntaxHighlighter)){
			return;
		}
		if(function_exists('add_options_page')){
			add_options_page('Google Syntax Highlighter Pro','Syntax Highlighter Pro',9,basename(__FILE__),array(&$googleSyntaxHighlighter,'SyntaxHighlighterPrintAdminPage'));
		}
	}

}
add_action('activate_syntaxhighlighter/wp-syntaxhighlighterpro.php',array(&$googleSyntaxHighlighter,
'init'));
add_action('wp_enqueue_scripts',array(&$googleSyntaxHighlighter,'loadResources'));
add_action('wp_footer',array(&$googleSyntaxHighlighter,'runscripts'));
add_action('admin_menu','SyntaxHighlighterAdminPanel');
?>