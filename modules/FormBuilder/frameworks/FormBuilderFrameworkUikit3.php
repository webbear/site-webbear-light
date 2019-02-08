<?php

/**
 * FormBuilder uikit framework definition file
 *
 * @property string $ukURL
 * @property string $inputSize
 * @property string $buttonSize
 * @property string $buttonType
 * @property string $buttonFull
 * @property bool $horizontal
 * @property int $horizHeaderWidth
 *
 */

class FormBuilderFrameworkUikit3 extends FormBuilderFramework {

	public function load() {

		$markup = array(
			'list' => "<div {attrs}>{out}</div>",
			'item' => "<div {attrs}>{out}</div>",
			'item_label' => "<label class='InputfieldHeader uk-form-label' for='{for}'>{out}</label>",
			'item_label_hidden' => "<label class='InputfieldHeader InputfieldHeaderHidden'><span>{out}</span></label>",
			'item_content' => "<div class='InputfieldContent uk-form-controls {class}'>{out}</div>",
			'item_error' => "<p class='uk-text-danger uk-margin-bottom-remove uk-margin-small'><i class='uk-icon-warning'></i> {out}</p>",
			'item_description' => "<p class='uk-text-muted uk-text-small uk-margin-small'>{out}</p>",
			'item_notes' => "<p class='uk-text-small uk-text-muted uk-margin-small'>{out}</p>",
			'success' => "<p class='uk-alert uk-alert-success'><i class='uk-icon-check'></i> {out}</p>",
			'error' => "<p class='uk-alert uk-alert-danger'><i class='uk-icon-warning'></i> {out}</p>",
			'item_icon' => "",
			'item_toggle' => "",
			'InputfieldFieldset' => array(
				'item' => "<fieldset {attrs}>{out}</fieldset>",
				'item_label' => "<legend class='uk-legend'>{out}</legend>",
				'item_label_hidden' => "<legend>{out}</legend>",
				'item_content' => "<div class='InputfieldContent'>{out}</div>",
				'item_description' => "<p class='uk-text-muted'>{out}</p>",
			)
		);

		$classes = array(
			'form' => 'InputfieldFormNoHeights',
			'list' => 'Inputfields',
			'list_clearfix' => 'uk-clearfix',
			'item' => 'Inputfield Inputfield_{name} {class}',
			'item_required' => 'InputfieldStateRequired',
			'item_error' => 'InputfieldStateError',
			'item_collapsed' => 'InputfieldStateCollapsed',
			'item_column_width' => 'InputfieldColumnWidth',
			'item_column_width_first' => 'InputfieldColumnWidthFirst',
			'InputfieldCheckboxes' => array('item_content' => 'uk-form-controls-text'),
			'InputfieldCheckbox' => array('item_content' => 'uk-form-controls-text'),
			'InputfieldRadios' => array('item_content' => 'uk-form-controls-text'),
			'InputfieldFieldset' => array('item' => 'Inputfield_{name} uk-fieldset {class}')
		);

		if((int) $this->horizontal) {
			// for uk-form-horizontal
			$classes['form'] .= " uk-form-horizontal InputfieldFormNoWidths";
			$markup['item_content'] = "<div class='InputfieldContent uk-form-controls {class}'>{out}{description}{notes}</div>";

			// the following duplicates the styles from uikit.css, but uses our custom widths
			// this is necessary because uikit only supports horizontal forms at 960px and above
			// and the width (200px) is fixed, when we need variable/user definable width.
			$mobilePx = $this->form->mobilePx;
			if(ctype_digit("$mobilePx")) $mobilePx = ((int) $mobilePx) . "px";
			if($mobilePx != '1px') $this->addInlineStyles("
				@media (min-width: $mobilePx) {
					.InputfieldForm.uk-form-horizontal .uk-form-label {
						display: block;
						margin-bottom: 5px;
						font-weight: bold;
					}
				}
				@media (min-width: $mobilePx) {
					.InputfieldForm.uk-form-horizontal .uk-form-label {
						width: {$this->horizHeaderWidth}%; 
						margin-top: 5px;
						float: left; 

					}
					.InputfieldForm.uk-form-horizontal .uk-form-controls {
						margin-left: {$this->horizHeaderWidth}%; 
						padding-left: 1em;
					}
					.InputfieldForm.uk-form-horizontal .uk-form-controls-text {
						padding-top: 5px;
					}
				}
				");
		} else {
			$classes['form'] .= " uk-form-stacked";
		}

		InputfieldWrapper::setMarkup($markup);
		InputfieldWrapper::setClasses($classes);

		$ukURL = $this->ukURL;
		$ukPath = null;
		if(strpos($ukURL, '//') !== false) {
			$ukURL = rtrim($ukURL, '/');
		} else {
			$ukPath = $this->wire('config')->paths->root . trim($ukURL, '/');
			$ukURL = $this->wire('config')->urls->root . trim($ukURL, '/');
		}

		$config = $this->wire('config');
		$css = $this->css;
		if(!$css) $css = 'uikit.gradient.min.css';
		$ukTheme = str_replace('uikit.', '', $css);

		if($this->allowLoad('framework')) {
			$config->styles->prepend("$ukURL/css/$css");
			$config->scripts->append("$ukURL/js/uikit.min.js");
		}
		$config->styles->append($config->urls->FormBuilder . 'FormBuilder.css');
		$config->inputfieldColumnWidthSpacing = 0;

		// load custom theme stylesheets, where found
		if(!$this->form->theme) $this->form->theme = 'delta';

		// change markup of submit button
		$this->addHookBefore('InputfieldSubmit::render', $this, 'hookInputfieldSubmitRender');
		$this->addHookBefore('FormBuilderProcessor::renderReady', $this, 'hookBeforeRenderReady');
	}

	public function hookBeforeRenderReady($event) {
		$inputfields = $event->arguments(0);
		$inputSizeClass = $this->inputSize;
		foreach($inputfields->getAll() as $in) {
			if($inputSizeClass) {
				$in->addClass("uk-form-" . $this->inputSize);
			}
			if($in instanceof InputfieldPage) {
				$className = $in->inputfield;
			} else {
				$className = $in->className();
			}
			if($in instanceof InputfieldTextarea) {
				$in->addClass('uk-textarea');	
			} else if($in instanceof InputfieldText || $in instanceof InputfieldInteger || $in instanceof InputfieldDatetime) {
				$in->addClass('uk-input');
			} else if($className == 'InputfieldSelect') {
				$in->addClass('uk-select');
			}
		}
	}

	/*
	public function hookAfterRenderReady($event) {
		if(strpos($event->return, ' fa-') !== false) {
			$event->return = str_replace(array('fa fa-fw fa-', 'fa fa-'), 'uk-icon-', $event->return); 
		}
	}
	*/

	public function hookInputfieldSubmitRender($event) {
		$in = $event->object;
		$event->replace = true;
		$classes = array('uk-button');
		if(!$this->buttonType) $this->buttonType = 'default';
		if($this->buttonSize) $classes[] = "uk-button-$this->buttonSize";
		if($this->buttonType) $classes[] = "uk-button-$this->buttonType";
		if($this->buttonFull) $classes[] = "uk-width-1-1";
		$class = implode(' ', $classes);
		$value1 = $this->wire('sanitizer')->entities($in->attr('value'));
		$value2 = $in->entityEncode($in->value, Inputfield::textFormatBasic);
		$event->return = "<button type='submit' name='$in->name' value='$value1' class='$class'>$value2</button>";
	}

	/**
	 * Return Inputfields for configuration of framework
	 *
	 * @return InputfieldWrapper
	 *
	 */
	public function getConfigInputfields() {
		$inputfields = parent::getConfigInputfields();
		$defaults = self::getConfigDefaults();
		$defaultLabel = $this->_('Default value:') . ' ';

		$f = $this->wire('modules')->get('InputfieldURL');
		$f->attr('name', 'ukURL');
		$f->label = $this->_('URL to Uikit framework');
		$f->description = $this->_('Specify a URL/path relative to root of ProcessWire installation.');
		$f->attr('value', $this->ukURL);
		if($this->ukURL != $defaults['ukURL']) $f->notes = $defaultLabel . $defaults['ukURL'];
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldRadios');
		$f->attr('name', 'css');
		$f->label = $this->_('Uikit CSS theme file');

		$_ukPath = $this->wire('forms')->frameworksPath() . 'uikit/css/';
		if(strpos($this->ukURL, '//') !== false) {
			// http URL, we can't identify CSS files there, so use our default 
			$ukPath = $_ukPath;
		} else {
			$ukPath = $this->wire('config')->paths->root . trim($this->ukURL, '/') . '/css/';
			if(!is_dir($ukPath)) {
				$f->error("Unable to locate path: $ukPath");
				$ukPath = $_ukPath;
			}
		}

		foreach(new DirectoryIterator($ukPath) as $file) {
			if($file->isDir() || $file->isDot() || $file->getExtension() != 'css') continue;
			$f->addOption($file->getBasename());
		}
		$f->attr('value', $this->css);
		$f->columnWidth = 33;
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldRadios');
		$f->attr('name', 'horizontal');
		$f->label = $this->_('Form style');
		$f->addOption(0, $this->_('Stacked (default)'));
		$f->addOption(1, $this->_('Horizontal (2-column)'));
		$f->attr('value', $this->horizontal);
		$f->optionColumns = 1;
		$f->description = $this->_('Please note that individual field column widths (if used) are not applicable when using the *Horizontal* style.');
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldInteger');
		$f->attr('name', 'horizHeaderWidth');
		$f->label = $this->_('Percent width for label columns (horizontal style only)');
		$f->description = $this->_('Specify a value between 5% and 90% percent to determine the width of the label column. The input column will have the remaining percent, i.e. if you specify 30% here, the label column will have 30% width and the input column will have 70% width.');
		$f->min = 5;
		$f->max = 90;
		$f->attr('value', $this->horizHeaderWidth);
		$f->showIf = 'frUikit3_horizontal=1';
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldRadios');
		$f->attr('name', 'inputSize');
		$f->label = $this->_('Input size');
		$f->addOption('small', $this->_('Small'));
		$f->addOption('', $this->_x('Normal', 'sizeType'));
		$f->addOption('large', $this->_('Large'));
		$f->attr('value', $this->inputSize);
		$f->columnWidth = 34;
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldRadios');
		$f->attr('name', 'buttonType');
		$f->label = $this->_('Submit button type');
		$f->addOption('default', $this->_('Default'));
		$f->addOption('primary', $this->_('Primary'));
		$f->addOption('secondary', $this->_('Secondary'));
		$f->addOption('danger', $this->_('Danger'));
		$f->attr('value', $this->buttonType);
		$f->columnWidth = 33;
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldRadios');
		$f->attr('name', 'buttonSize');
		$f->label = $this->_('Submit button size');
		//$f->addOption('mini', $this->_('Mini'));
		$f->addOption('small', $this->_('Small'));
		$f->addOption('', $this->_('Normal'));
		$f->addOption('large', $this->_('Large'));
		$f->attr('value', $this->buttonSize);
		$f->columnWidth = 33;
		$inputfields->add($f);

		$f = $this->wire('modules')->get('InputfieldCheckbox');
		$f->attr('name', 'buttonFull');
		$f->label = $this->_('Full width button?');
		if($this->buttonFull) $f->attr('checked', 'checked');
		$f->columnWidth = 34;
		$inputfields->add($f);

		return $inputfields;
	}

	public function getConfigDefaults() {
		$urls = $this->wire('config')->urls;
		$ukURL = str_replace($urls->root, '/', $urls->FormBuilder . 'frameworks/uikit3/');
		return array_merge(parent::getConfigDefaults(), array(
			'ukURL' => $ukURL,
			'horizontal' => 0,
			'horizHeaderWidth' => 30,
			'css' => 'uikit.min.css',
			'inputSize' => '',
			'buttonType' => '',
			'buttonSize' => '',
			'buttonFull' => 0,
		));
	}

	public function getFrameworkURL() {
		return $this->ukURL;
	}

}
