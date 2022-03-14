<?php

namespace App\Exports;

use App\Custom\Report;
use App\Score;
use App\User;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ScoreTotalExport
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	/**
	 * @deprecated я так понимаю теперь используем @see ScoreExport::excel()
	 */
	public function view(): View
	{
		$filter = Report::createFilter($this->param);

		return view('report.score-total-table', [
			'scores' => Report::searchResult($filter),
			'weightedAverage' => Report::weightedAverageScore($filter)
		]);
	}

	public static function excel($param) {

        $filter = Report::createFilter($param);

		$schedule = Score::studentSchedule(User::find($filter['student_id']['value']));
        $scores = Report::searchResult($filter);
        $attendance = Report::attendanceStudent($filter);
        $weightedAverage = Report::weightedAverageScore($filter);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimensionByColumn(1)->setWidth(40);
        $sheet->getColumnDimensionByColumn(2)->setWidth(40);
        $sheet->getColumnDimensionByColumn(3)->setWidth(15);
        $sheet->getColumnDimensionByColumn(4)->setWidth(30);

        $row = 1;
        $sheet->setCellValue('A'.$row, 'Предмет');
        $sheet->setCellValue('B'.$row, 'Оценки');
        $sheet->setCellValue('C'.$row, 'Средний балл');
        $sheet->setCellValue('D'.$row, 'Посещаемость');

        $row = 2;

		foreach($schedule as $item) {
            $sheet->setCellValue('A'.$row, $item->lesson->name);
            $richText = new RichText();

			if (isset($scores[$item->lesson->id])) {
				foreach($scores[$item->lesson->id]['scores'] as $score) {
					if(!empty($score['value'])) {
						$richText->createText($score['value']);
						$weight = $richText->createTextRun($score['weight']);
						$weight->getFont()->setSubscript(true);
						$richText->createText(' ');
					}
				}
			}

            $sheet->setCellValue('B'.$row, $richText);
            $sheet->setCellValue('C'.$row, isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-');
            $late = $attendance[$item->lesson->id]->late ?? 0;
            $absent = $attendance[$item->lesson->id]->absent ?? 0;

            $attendanceText = "Опозданий на {$late} мин. Не был на {$absent} ур.";
            $sheet->setCellValue('D'.$row, $attendanceText);
            $row++;
        }

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"scores.xlsx\"");
        header("Cache-Control: max-age=0");

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
