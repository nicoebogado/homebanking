<?php
Yii::import('ext.pdf.fpdf.fpdf');

class PDF extends fpdf
{
	public $widths;
	public $aligns;
	public $rowHeight = 6;
	//longitud total de la tabla
	public $tableWidth = 275;

	/**
	 * @var function Render the (main) header
	 */
	public $headerRenderizer;

	/**
	 * @var function Render the (main) footer
	 */
	public $footerRenderizer;

	/**
	 * @var function Render the header for pages since second page
	 */
	public $secondHeaderRenderizer;

	/**
	 * @var function Render the footer for pages since second page
	 */
	public $secondFooterRenderizer;
	
	// Cabecera de p�gina
	public function Header()
	{
		if (
			is_callable($this->headerRenderizer) &&
			(
				$this->PageNo() == 1 ||
				!is_callable($this->secondHeaderRenderizer)
			)
		) {
			$this->headerRenderizer($this);
		} elseif (is_callable($this->secondHeaderRenderizer)) {
			$this->secondHeaderRenderizer($this);
		}
	}

	function Footer()
	{
		// Go to 1.5 cm from bottom
		$this->SetY(-15);
		// Select Arial italic 8
		$this->SetFont('Arial','I',8);
		// Print centered page number
		//$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
		$this->Cell(0,10,$this->footerRenderizer,0,0,'C');

		/*if (
			is_callable($this->footerRenderizer) &&
			(
				$this->PageNo() == 1 ||
				!is_callable($this->secondFooterRenderizer)
			)
		) {
			$this->footerRenderizer($this);
		} elseif (is_callable($this->secondFooterRenderizer)) {
			$this->secondFooterRenderizer($this);
		}*/
	}
	
	public function SetBold()
	{
		$this->setFont('', 'B');
	}
	
	public function SetItalic()
	{
		$this->setFont('', 'I');
	}
	

	public function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	//Set the array of column alignments
	public function SetAligns($a)
	{
		$this->aligns=$a;
	}

	/**
	 * $config puede tener:
	 * 		border=>true/false
	 * 		fill=>true/false
	 */
	public function Row($data, $config)
	{
		$config['border'] = !empty($config['border']);
		$config['fill'] = !empty($config['fill']);
		$config['header'] = !empty($config['header']);
	
		//Calculate the height of the row
		$nb	= $this->NbLines($data);
		$h	= $this->rowHeight*max($nb);
		
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++) {
			$w=$this->widths[$i];
			if($config['header'])
				$a = 'C';
			else
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			
			//Draw the border
			if($config['border'])
				$this->Rect($x, $y, $w, $h);
				
			//Print the text
			$this->MultiCell($w, $h/$nb[$i], $data[$i], 0, $a, $config['fill']);
			
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		
		//Go to the next line
		$this->Ln($h);
	}

	private function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	//Computes the number of lines a MultiCell of width w will take
	private function NbLines($data)
	{
		$resp = array();
		for($n=0;$n<count($data);$n++) {
			$w		= $this->widths[$n];
			$txt	= $data[$n];
			
			$cw=&$this->CurrentFont['cw'];
			
			if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
			
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r", '', $txt);
			$nb=strlen($s);
			
			if($nb>0 and $s[$nb-1]=="\n")
				$nb--;
			
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb) {
				$c=$s[$i];
				
				if($c=="\n") {
					$i++;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
					continue;
				}
				
				if($c==' ')
					$sep=$i;
					
				$l+=$cw[$c];
				if($l>$wmax) {
					if($sep==-1)
					{
						if($i==$j)
							$i++;
					} else
						$i=$sep+1;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
				}
				else
					$i++;
			}
			
			$resp[] = $nl;
		}
		
		return $resp;
	}

	public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
    }
}