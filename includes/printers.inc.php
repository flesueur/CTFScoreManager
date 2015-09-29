<?

class Table {

	private $header = array();    
	private $content = array();
	private $labels = array();
	private $colors = array();


//    function __construct() {
//    }

	public function __toString() {
		$str = "<table class=\"pure-table\"><thead><tr>";
		foreach ($this->header as $value) {
			$str .= "<td>$value</td>";
		}
		$str .= "</tr></thead><tbody>\n";
		$odd = true;
		$class = "";
		$id = "";
		$color = "";
		$i=0;
		foreach ($this->content as $row) {
			if ($odd) {
				$color = "bgcolor =\"".$this->colors[$i]."\"";
				//$class = "class=\"pure-table-odd\"";
			} else {
				$color = "bgcolor =\"".lighten($this->colors[$i])."\"";
				//$class = "";
			}
			$odd = !$odd;

			if ($this->labels[$i+1] != NULL)
			{
				$id = "id =\"".$this->labels[$i+1]."\"";
			}

			$str .= "<tr $id $class $color>";
			foreach ($row as $value) {
				$str .= "<td>$value</td>";
			}
			$str .= "</tr>\n";
			$i++;
		}
		$str .= "</tbody></table>\n";
		return $str;
	}

	function setHeader($header) {
		$this->header = $header;
	}

	function addRow($row, $label=NULL, $color=NULL) {
		$this->content[] = $row;
		$this->labels[] = $label;
		$this->colors[] = $color;
	}


}

class Formular {

	private $formular = "";
	private $action;
	private $method = "POST";

	function __construct($action, $method) {
		$this->action = $action;
		$this->method = $method;
	}

	function setAction($action) {
		$this->action = $action;
	}

	function setMethod($method) {
		$this->method = $method;
	}

	public function __toString() {
		$res = "<form method=\"$this->method\" action=\"$this->action\"  class=\"pure-form pure-form-aligned\"><fieldset>";
		$res .= $this->formular;
		$res .= "</fieldset></form>";
		return $res;
	}

	public function toStringNoFieldset() {
		$res = "<form method=\"$this->method\" action=\"$this->action\">";
		$res .= $this->formular;
		$res .= "</form>";
		return $res;
	}

	function addHidden($name, $value) {
		$this->formular .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>";
	}

	function addTextarea($name, $title, $holder, $default) {
		$this->formular .= "<div class=\"pure-control-group\">
			<label for=\"$name\">$title</label>
            		<textarea id=\"$name\" type=\"text\" placeholder=\"$holder\" name=\"$name\" rows=\"8\" cols=\"50\">$default</textarea>
		        </div>";
	}

	function addText($name, $title, $holder, $default) {
		$this->formular .= "<div class=\"pure-control-group\">
           		 <label for=\"$name\">$title</label>
            		<input id=\"$name\" type=\"text\" value=\"$default\" name=\"$name\" placeholder=\"$holder\">
      			  </div>";
	}

	function addPassword($name, $title, $holder, $default) {
		$this->formular .= "<div class=\"pure-control-group\">
           		 <label for=\"$name\">$title</label>
            		<input id=\"$name\" type=\"password\" value=\"$default\" name=\"$name\" placeholder=\"$holder\">
      			  </div>";
	}

	function addLabel($label) {
		$this->formular .= "<div class=\"pure-controls\">
		<label for=\"cb\" class=\"pure-checkbox\">$label</label>
		</div>";
	}

	function addRadio($name, $default, $choices) {
		$this->formular .= "<div class=\"pure-controls\">";
		foreach ($choices as $key=>$value) {
			$this->formular .= "<label for=\"$name\" class=\"pure-radio\">
        <input id=\"$name-$key\" type=\"radio\" name=\"$name\" value=\"$key\"";
			if ($key == "$default") $this->formular .= " checked";
			$this->formular .= "> $value
	</label>";
		}
    		$this->formular .= "</div>";
	}

	function addCheckbox($name, $label) {
		$this->formular .= "<div class=\"pure-controls\">
            <label for=\"$name\" class=\"pure-checkbox\">
                <input id=\"$name\" type=\"checkbox\" name=\"$name\"> $label
            </label>
		</div>";
	}

	function addDropdown($name, $label, $choices, $default) {
		$this->formular .= "<div class=\"pure-control-group\"><label for=\"$name\">$label</label>
					<select id=\"$name\" name=\"$name\">";
		foreach ($choices as $key=>$value) {
			$this->formular .= "<option value=\"$key\"";
			if ($key == "$default") $this->formular .= " selected";
			$this->formular .= ">$value</option>";
		}
    		$this->formular .= "</select></div>";
	}

	function addSubmitButton() {
		$this->formular .= "<div class=\"pure-controls\">
           		 <button type=\"submit\" class=\"pure-button pure-button-primary\">Valider</button>
     			   </div>";
	}

	function addSubmitButtonNoDiv($color, $default) {
		$this->formular .= "<button type=\"submit\" class=\"pure-button pure-button-primary $color\">$default</button>";
	}

	function addValueButtonNoDiv($color, $default, $name, $value) {
		$this->formular .= "<button type=\"submit\" class=\"pure-button pure-button-primary $color\" name=\"$name\" value=\"$value\">$default</button>\n";
	}
}


// Gestion des couleurs
function lighten($color) {
	$colors = array("green"=>"lightgreen", "red"=>"salmon", "darkorange"=>"orange", "gray"=>"white");
	$res = $colors[$color];
	if ($res == NULL) {$res = "#E0E0E0";}
	return $res;
}


?>
