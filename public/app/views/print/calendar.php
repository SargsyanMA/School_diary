<?if(!empty($week)):?>
    <h1><?=$title;?></h1>
    <?foreach($week as $num=>$day):?>
        <? if ($printDay && $printDay!=$num) continue;?>
        <table class="table table-condensed table-bordered calendar table-striped table-hover" style="page-break-after: always;">
            <tbody>
                <tr class="page-break">
                    <th  class="day-header day-date" colspan="6" id="day-<?=$num;?>">
                        <?=$day['name']['name'];?> <?=$day['date'];?>
                    </th>
                </tr>
                <tr class="header">
                    <th class="lesson-number">#</th>
                    <th class="lesson-time">Время</th>
                    <th>Предмет</th>
                    <?if($mode=='teacher'):?>
                        <th>Класс</th>
                    <?else:?>
                        <th>Педагог</th>
                    <?endif;?>
                    <th style="width:30%">Домашнее задание</th>
                    <th style="width:30%">Комментарий</th>
                </tr>
                <?foreach($day['lessons'] as $num=>$lesson):?>
                    <tr>
                        <td class="lesson-number" rowspan="<?=count($lesson['lessons'])?count($lesson['lessons']):1;?>">
                            <?=$lesson['name'];?>
                        </td>
                        <td class="lesson-time" rowspan="<?=count($lesson['lessons'])?count($lesson['lessons']):1;?>">
                            <?=$lesson['time'][0];?> - <?=$lesson['time'][1];?>
                        </td>
                        <?if(!empty($lesson['lessons'])):?>
                            <?foreach ($lesson['lessons'] as $lsnNum=>$lsn):?>
                                <?if($lsnNum>0):?></tr><tr><?endif;?>

                                <td class="lesson">
                                    <strong><?=$lsn['lessonName'];?></strong>
                                    <?if ($lsn['type']=='individual'):?>
                                        <div class="student" data-student="<?=$lsn['student'];?>">
                                            <?if(!empty($lsn['students'])) foreach($lsn['students'] as $studentId):?>
                                                <?=$students[$studentId]['name'];?>
                                            <?endforeach;?>
                                            <?=$lsn['student'];?>
                                            <span class="badge badge-warning"><small>ИНД.</small></span>
                                        </div>
                                    <?endif;?>
                                </td>
                                <?if($mode=='teacher'):?>
                                    <td class="grade"><?=$lsn['gradeName'];?></td>
                                <?else:?>
                                    <td class="teacher"><?=$lsn['teacherName'];?></td>
                                <?endif;?>
                                <td style="width:30%">
                                    <div class="edit-content homework">
                                        <?=$lsn['homework']['text'];?>
                                    </div>
                                </td>
                                <td style="width:30%">
                                    <div class="edit-content comment">
                                        <?=$lsn['comment']['text'];?>
                                    </div>
                                </td>
                                <?if($lsnNum>0 && $lsnNum<(count($lesson['lessons'])-1)):?></tr><?endif;?>
                            <?endforeach;?>
                        <?else:?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        <?endif;?>
                    </tr>
                <?endforeach;?>
            </tbody>
        </table>
    <?endforeach;?>
<?endif;?>