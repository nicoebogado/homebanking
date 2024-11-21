<?php

Yii::import('zii.widgets.grid.CDataColumn');
Yii::import('ext.pdf.fpdf.PDF');

class EPDF extends CWidget
{
	private $_debug = false;

	protected $_pdf;
	protected $_fill = false;

	// valores que cambian en cada iteracion dentro del init()
	protected $_columnWidths = array();
	protected $_visibleColumns = 0;
	protected $dataProvider;
	protected $columns = array();
	protected $tableTitle;

	public $fileName;
	public $datas;
	public $rowHeight = 6;
	public $headerHeight = 6;
	public $config;
	/**
	 * @var boolean whether to display the table even when there is no data. Defaults to true.
	 * The {@link emptyText} will be displayed to indicate there is no data.
	 */
	public $showTableOnEmpty = true;
	/**
	 * @var string the text to be displayed in a data cell when a data value is null.
	 * This property will NOT be HTML-encoded when rendering. Defaults to an HTML blank.
	 */
	public $nullDisplay = ' ';

	/**
	 * @var string the message to be displayed when {@link dataProvider} does not have any data.
	 */
	public $emptyText;
	/**
	 * @var boolean whether to hide the header cells of the grid. When this is true, header cells
	 * will not be rendered. Defaults to false.
	 */
	public $hideHeader = false;

	/**
	 * Creates column objects and initializes them.
	 */
	public function init()
	{
		$defaultConfig = array(
			'pdfSize'					=> 'A4',
			'headerRenderizer'			=> null,
			'secondHeaderRenderizer'	=> null,
			'footerRenderizer'			=> null,
			'tableWidth'				=> 190,
		);
		$config = array_merge($defaultConfig, $this->config);

		$this->_pdf = new PDF('P', 'mm', $config['pdfSize']);
		$this->_pdf->headerRenderizer = $config['headerRenderizer'];
		$this->_pdf->secondHeaderRenderizer = $config['secondHeaderRenderizer'];
		$this->_pdf->footerRenderizer = $config['footerRenderizer'];
		$this->_pdf->tableWidth = $config['tableWidth'];
		$this->_pdf->SetFont('Arial', 'b', 7.5);
		$this->_pdf->SetLineWidth(0.1);
		//$this->_pdf->rowHeight = $this->rowHeight;
		$this->_pdf->rowHeight = 11;
		$this->_pdf->AliasNbPages();
		$this->_pdf->AddPage();

		foreach ($this->datas as $table) {
			$this->dataProvider = isset($table['dataProvider']) ? $table['dataProvider'] : null;
			$columns = isset($table['columns']) ? $table['columns'] : null;
			$this->tableTitle = isset($table['title']) ? $table['title'] : null;
			$columnWidths = isset($table['columnWidths']) ? $table['columnWidths'] : null;
			$colAligns = isset($table['colAligns']) ? $table['colAligns'] : null;
			$this->_visibleColumns = 0;

			if (!$columns) {
				if ($this->dataProvider instanceof CActiveDataProvider)
					$this->columns = $this->dataProvider->model->attributeNames();
				else if ($this->dataProvider instanceof IDataProvider) {
					// use the keys of the first row of data as the default columns
					$data = $this->dataProvider->getData();
					if (isset($data[0]) && is_array($data[0]))
						$this->columns = array_keys($data[0]);
				}
			} else {
				$this->columns = $columns;
			}

			$id = $this->getId();
			foreach ($this->columns as $i => $column) {
				if (is_string($column))
					$column = $this->createDataColumn($column);
				else {
					if (!isset($column['class']))
						$column['class'] = 'CDataColumn';
					$column = Yii::createComponent($column, $this);
				}
				if (!$column->visible) {
					unset($this->columns[$i]);
					continue;
				}
				$this->_visibleColumns++;
				if ($column->id === null)
					$column->id = $id . '_c' . $i;
				$this->columns[$i] = $column;
			}
			$this->_pdf->SetAligns($colAligns);
			$this->_columnWidths = $this->_calcWidths($columnWidths);
			$this->_pdf->SetWidths($this->_columnWidths);

			foreach ($this->columns as $column)
				$column->init();

			$this->renderItems();
		}

		if ($this->_debug)
			Yii::app()->end();
		else {
			$this->_pdf->Output($this->fileName . ' (' . date('Y-m-d') . ').pdf', 'D');
			exit();
		}
	}

	/**
	 * Creates a {@link CDataColumn} based on a shortcut column specification string.
	 * @param string $text the column specification string
	 * @return CDataColumn the column instance
	 */
	protected function createDataColumn($text)
	{
		if (!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches))
			throw new CException(Yii::t(
				'zii',
				'The column must be specified in the format of "Name:Type:Label",
				where "Type" and "Label" are optional.'
			));
		$column = new CDataColumn($this);
		$column->name = $matches[1];
		if (isset($matches[3]) && $matches[3] !== '')
			$column->type = $matches[3];
		if (isset($matches[5]))
			$column->header = $matches[5];
		return $column;
	}

	/**
	 * Renders the data items for the grid view.
	 */
	protected function renderItems()
	{
		if ($this->dataProvider->getItemCount() > 0 || $this->showTableOnEmpty) {
			$this->renderTableHeader();
			$this->renderTableBody();
		} else
			$this->_renderEmptyText();
	}

	/**
	 * Renders the table header.
	 */
	protected function renderTableHeader()
	{
		if (!$this->hideHeader) {
			// titulo de la tabla
			$this->_pdf->SetBold();
			$this->_pdf->Ln();
			$this->_pdf->Cell(array_sum($this->_columnWidths), $this->rowHeight, $this->tableTitle, 0, 1, 'L');
			// Colores de fuente
			$this->_pdf->SetFillColor(135, 127, 125);
			$this->_pdf->SetTextColor(255);

			$rowHeader = array();

			foreach ($this->columns as $i => $column) {
				$rowHeader[] = $column->header;
			}
			$this->_pdf->Row($rowHeader, array('fill' => true, 'header' => true));
		}
	}

	/**
	 * Renders the table body.
	 */
	protected function renderTableBody()
	{
		$data = $this->dataProvider->getData();
		$n = count($data);

		// Restauración de colores y fuentes
		$this->_pdf->SetFillColor(214, 207, 199);
		$this->_pdf->SetTextColor(0);
		$this->_pdf->SetFont('Arial', '', 8);
		$this->_pdf->rowHeight = $this->rowHeight;

		if ($n > 0) {
			for ($row = 0; $row < $n; ++$row)
				$this->renderTableRow($row);
		} else
			$this->_renderEmptyText();
	}

	/**
	 * Renders a table body row.
	 * @param integer $row the row number (zero-based).
	 */
	protected function renderTableRow($row)
	{
		$rowData = array();
		foreach ($this->columns as $i => $column) {
			$data = $this->dataProvider->data[$row];

			if ($column->value !== null)
				$value = $column->evaluateExpression($column->value, array('data' => $data, 'row' => $row));
			else if ($column->name !== null)
				$value = CHtml::value($data, $column->name);

			$rowData[] = $value === null ? $this->nullDisplay : $this->_formatString($value);
		}
		$this->_pdf->Row($rowData, array('fill' => $this->_fill));
		$this->_fill = !$this->_fill;
	}

	/**
	 * Renders the empty message when there is no data.
	 */
	protected function _renderEmptyText()
	{
		$emptyText = $this->emptyText === null ? Yii::t('zii', 'No results found.') : $this->emptyText;
		$this->_pdf->Cell(array_sum($this->_columnWidths), $this->rowHeight, $emptyText, 0, 0, 'L');
	}

	protected function _calcWidths($params)
	{
		$widths = array();
		$visibleCols = $this->_visibleColumns;

		if (!$params) {

			$w = $this->_pdf->tableWidth / $visibleCols;
			for ($i = 0; $i < $visibleCols; $i++)
				$widths[] = $w;
		} else if (is_array($params)) {

			//verificar que la cantidad de los parametros no supere a la cantidad de columnas visibles
			if (count($params) > $visibleCols)
				throw new Exception('La cantidad de parametros supera a las columnas visibles');
			//verificar que la suma de los parametros no supere a la longitud max de la tabla
			if (array_sum($params) > $this->_pdf->tableWidth)
				throw new Exception('La suma de los parametros supera a la longitud max de la tabla');

			$nulls = 0; //cantidad de columnas que no se configuraron
			$confWidth = 0; //longitud total de las columnas que se configur�
			for ($i = 0; $i < $visibleCols; $i++) {
				if (empty($params[$i]))
					$nulls++;
				else
					$confWidth += $params[$i];
			}

			//establecer la longitud de las columnas que no fueron configuradas
			$w = $nulls ? ($this->_pdf->tableWidth - $confWidth) / $nulls : 0;

			//establecer la longitud de cada columna
			for ($i = 0; $i < $visibleCols; $i++) {
				$widths[] = empty($params[$i]) ? $w : $params[$i];
			}
		} else
			throw new Exception('El parametro $config[widths] debe ser un array');

		return $widths;
	}

	protected function _formatString($string)
	{
		$string = strtolower(utf8_decode($string));
		return ucwords($string);
	}

	/**
	 * Combina columnas e imprime un texto
	 * @param string $print Texto a imprimir
	 * @param mixed $config Permite las siguientes configuraciones:
	 * 		from: Nro de columna (cero based) desde la cual se est� imprimiendo, default: 0
	 * 		to: Nro de columna (cero based) hasta la cual se imprimir�, default: �ltima columna
	 * 		border: Imprimir bordes, default: 0
	 * 		align: Alineaci�n del texto, default: 'L'
	 * 		fill: Imprimir relleno, default: $this->_fill
	 * 		ln: parametro ln para fpdf::Cell(), default: 1
	 */
	protected function _combineColumns($print = '', $config = array())
	{
		$defaultConfig = array(
			'from'				=> 0,
			'to'				=> $this->_visibleColumns - 1,
			'border'			=> 0,
			'align'				=> 'L',
			'fill'				=> $this->_fill,
			'ln'				=> 1,
		);
		$c = array_merge($defaultConfig, $config);

		$w = 0;
		for ($i = $c['from']; $i <= $c['to']; $i++) {
			$w += $this->_columnWidths[$i];
		}

		$this->_pdf->Cell($w, $this->rowHeight, $print, $c['b'], $c['ln'], $c['a'], $f);
		if ($c['f']) $this->_fill = !$this->_fill;
	}

	protected function _setConfig($var, $attribute, $default = null)
	{
		return isset($var[$attribute]) ? $var[$attribute] : $default;
	}
}
