<?php

namespace App\Exports;

use App\Custom\Year;
use App\FormKrPlan;
use App\Grade;
use App\Holiday;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
USE \PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;

class FormKrPlanExport implements FromView
{
	public $param;

	public static $lastDayPastYear = 0;

	public function __construct($param) {
		$this->param = $param;
	}

	/**
	 * @deprecated looks like not using anymore
	 * @return View
	 */
	public function view(): View {
        $grades = Grade::query()
            ->where('year', '<=', Year::getInstance()->getYear() - 3)
            ->where('year', '>=', Year::getInstance()->getYear() - 10)
            ->where('letter', '<>', 'О')
            ->orderBy('year', 'desc')
            ->get(); // с четвертого класса
		$currentGrade = $this->param->request->get('grade_id', $grades->first()->id);

		return view("forms.kr-plan.print", [
			'dates' => FormKrPlan::periodsForKr(),
			'grades' => $grades,
			'currentGrade' => $currentGrade,
			'grade' => Grade::find($currentGrade),
			'krs' => FormKrPlan::getKr(1, $currentGrade)
		]);
	}

	/**
	 * @param $param
	 */
	public static function excel($param) {
		$holidayDays = self::getHolidayDays();
		$dates = FormKrPlan::periodsForKrPrint();
        $grades = Grade::query()
            ->where('year', '<=', Year::getInstance()->getYear() - 3)
            ->where('year', '>=', Year::getInstance()->getYear() - 10)
            ->where('letter', '<>', 'О')
            ->orderBy('year', 'desc')
            ->get(); // с четвертого класса
		$currentGrade = $param->request->get('grade_id', $grades->first()->id);
		$krs = FormKrPlan::getKr(1, $currentGrade);

		$spreadsheet = self::getSpreadsheet();
		$sheetNum = 0;
		$spreadsheet->createSheet($sheetNum);

		foreach ($dates as $year_num => $year) {
			$sheet = $spreadsheet->getSheet($sheetNum);
			$sheet->setTitle($sheetNum + 1 .' полугодие');
			$row = 1;

			self::setWidth($sheet);
			self::getHead($sheet, $row, $sheetNum + 1);
			self::getNotice($sheet, $row);
			self::getTableHead($sheet, $row);

			$sheetNum++;
			foreach ($year as $week) {
				$hasWorkDays = self::getDatesHead($sheet, $row, $week);
				if ($hasWorkDays) {
					self::getKr($sheet, $row, $week, $krs, $holidayDays, $grades);
				}
			}
		}

		self::setHeaders();

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * @return Spreadsheet Spreadsheet
	 */
	private static function getSpreadsheet() {
		$styleArray = ['font' => ['name' => 'Times New Roman']];

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getProperties()->setCreator('life.theschool.ru')
			->setLastModifiedBy('life.theschool.ru')
			->setTitle('График контрольных работ')
			->setSubject('График контрольных работ')
			->setDescription('График контрольных работ');

		$spreadsheet->getDefaultStyle()->applyFromArray($styleArray);

		return $spreadsheet;
	}

	/**
	 * @param Worksheet $sheet
	 */
	private static function setWidth(&$sheet) {
		for ($i = 0; $i < 7; $i++) {
			$sheet->getColumnDimensionByColumn($i)->setWidth(20);
		}
	}

	/**
	 * @param Worksheet $sheet
	 * @param int $row
	 * @param $sheetNum
	 */
	private static function getHead(&$sheet, &$row, $sheetNum) {
		$sheet->mergeCells('A'.$row.':F'.$row);
		$sheet->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A'.$row.':F'.$row)->getFont()->setSize(14)->setBold(true);
		$sheet->setCellValue('A'.$row, 'График контрольных работ СРЕДНЕЙ ШКОЛЫ на '.$sheetNum.' полугодие'.self::yearsRange().'уч.г.');
		$row++;
	}

	/**
	 * @param Worksheet $sheet
	 * @param int $row
	 */
	private static function getNotice(&$sheet, &$row) {
		$sheet->mergeCells('A'.$row.':F'.$row);
		$sheet->getStyle('A'.$row.':F'.$row)->getAlignment()->setWrapText(true);
		//@todo так и не понял работатет ли это
		foreach ($sheet->getRowDimensions($row) as $dimension) {
			$dimension->setRowHeight(-1);
		}

		$sheet->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A'.$row.':F'.$row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
		$sheet->getStyle('A'.$row.':F'.$row)->getFont()->setSize(11)->setBold(true);
		$sheet->setCellValue('A2', 'Уважаемые коллеги! В один день может быть проведено не более 2 контрольных работ.
		Все работы должны быть проведены в тот день, в который они указаны. В случае изменения даты к\р,
		сообщите об этом Ефановой ИВ не позднее, чем за неделю до проведения работы. Если ВЫ не сообщили об изменениях,
		контрольная работа проводиться не может!');
		$row++;
	}

	/**
	 * @param Worksheet $sheet
	 * @param int $row
	 */
	private static function getTableHead(&$sheet, &$row) {
		$sheet->getStyle('A'.$row.':F'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
		$sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
		$sheet->setCellValue('A'.$row, '');
		$sheet->setCellValue('B'.$row, 'Понедельник');
		$sheet->setCellValue('C'.$row, 'Вторник');
		$sheet->setCellValue('D'.$row, 'Среда');
		$sheet->setCellValue('E'.$row, 'Четверг');
		$sheet->setCellValue('F'.$row, 'Пятница');
		$row++;
	}

	/**
	 * @param Worksheet $sheet
	 * @param int $row
	 * @param Carbon[] $week
	 * @return bool
	 */
	private static function getDatesHead(&$sheet, $row, $week) {
		$column = 'A';
		$hasWorkDays = false;
		$sheet->setCellValue($column.$row, '');
		for ($i = 0; $i <= 4; $i++) {
			++$column;
			if (isset($week[$i])) {
				if (!$hasWorkDays) {
					$hasWorkDays = true;
					$sheet->getStyle('A'.$row.':F'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
				}

				$date = Carbon::parse($week[$i])->locale('ru')->isoFormat('D MMM');
				$sheet->setCellValue($column.$row, $date);
			}
		}
		return $hasWorkDays;
	}

	/**
	 * @param Worksheet $sheet
	 * @param int $row
	 * @param Carbon[] $week
	 * @param $krs
	 * @param $holidayDays
	 */
	private static function getKr(&$sheet, &$row, $week, $krs, $holidayDays, $grades) {
		$row++;
		foreach ($grades as $grade) {
			$column = 'A';
			$sheet->setCellValue($column.$row, $grade->numberLetter.' центр');
			$sheet->getStyle($column.$row)->getFont()->setBold(true);
			for ($i = 0; $i <= 4; $i++) {
				++$column;
				if (isset($week[$i])) {
					if (in_array($week[$i]->format('Y-m-d'), $holidayDays)) {
						$sheet->getStyle($column.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');
					} else {
						if (isset($krs[$week[$i]->format('Y-m-d')][$grade->id])) {
                            $val = '';
							foreach ($krs[$week[$i]->format('Y-m-d')][$grade->id] as $kr) {
								if (!empty($kr->lesson)) {
								    $val .= $kr->lesson->name.' - '.$kr->text."\n" ?? '';
								}
							}
							$sheet->setCellValue($column.$row,$val);
                            $sheet->getStyle($column.$row)->getAlignment()->setWrapText(true);
                            foreach ($sheet->getRowDimensions($row) as $dimension) {
                                $dimension->setRowHeight(-1);
                            }
						}
					}
				}
			}
			$row++;
		}
	}

	/**
	 * @return void
	 */
	private static function setHeaders() {
		$fileName = 'График контрольных работ СРЕДНЕЙ ШКОЛЫ на '.self::yearsRange().'уч.г.';

		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"$fileName.xlsx\"");
		header("Cache-Control: max-age=0");
	}

	/**
	 * @return string
	 */
	private static function yearsRange() {
		$year = Year::getInstance()->getYear();
		return $year.'-'.($year + 1);
	}

	/**
	 * @return array
	 */
	private static function getHolidayDays() {
		$holidayDays = Holiday::getHolidaysDaysArray(Year::getInstance()->getYear());
		$holidayDaysFormatted = [];
		foreach ($holidayDays as $day) {
			$holidayDaysFormatted[] = $day->format('Y-m-d');
		}

		return $holidayDaysFormatted;
	}
}
