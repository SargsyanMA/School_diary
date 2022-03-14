<?if(!empty($schedule)):?>
    <h1><?=$title;?></h1>
    <table class="table schedule table-bordered">
        <thead>
            <tr>
                <th class="lesson-num-time">#</th>
                <?foreach ($schedule['weekDays'] as $dayName):?>
                    <th><?=$dayName;?></th>
                <?endforeach;?>
            </tr>
        </thead>
        <tbody>
            <?foreach ($schedule['lessons'] as $lessonNum => $lessons):?>
                <tr>
                    <th>
                        <span class="lesson-num"><?=$lessonDict[$lessonNum-1]['name'];?></span>
                        <span class="lesson-time"><?=implode(' - ',$lessonDict[$lessonNum-1]['time']);?></span>
                    </th>
                    <?foreach ($lessons as $dayNum => $lesson):?>
                        <td data-lesson-num="<?=$lesson['number'];?>" data-day-num="<?=$lesson['weekday'];?>"  class="weekday">
                            <?if(!empty($lesson['lessons'])):?>
                                <?foreach ($lesson['lessons'] as $lsn):
                                    if (!$lsn['active']) continue;
                                    //if ($lsn['lessonType']=='zhome' && !$lastName) continue;
                                    ?>
                                    <div class="data" data-id="<?=$lsn['id'];?>" >
                                        <div class="lesson" data-lesson="<?=$lsn['lesson'];?>">
                                            <?=$lsn['lessonName'];?>
                                            <?if ($lsn['type']=='individual'):?>
                                                <span class="badge badge-warning">ИНД.</span>
                                            <?endif;?>
                                        </div>
                                        <?if ($currentType!='teacher'):?>
                                            <div class="teacher" data-teacher="<?=$lsn['teacher'];?>"><?=$lsn['teacherName'];?></div>
                                        <?else:?>
                                            <div class="grade"><?=$lsn['gradeName'];?></div>
                                        <?endif;?>

                                        <?if ($lsn['note']):?>
                                            <div class="note"><i class="fa fa-exclamation-circle"></i> <?=$lsn['note'];?></div>
                                        <?endif;?>

                                        <?if(!empty($lsn['students']) && $currentType!='student'):?>
                                            <?if($lsn['type']=='individual' || $lastName || $lsn['lessonType']=='psylog'):?>
                                                <ol class="students">
                                                    <?foreach ($lsn['students'] as $num => $studentId):?>
                                                        <li><?=User::getInstance()->getShortName($students[$studentId]['name']);?></li>
                                                    <?endforeach;?>
                                                </ol>
                                            <?endif;?>
                                        <?endif;?>
                                        <?if ($lsn['allClass'] && $lastName):?>
                                            Весь класс: <?=$lsn['studentsCount'];?>
                                        <?endif;?>
                                    </div>
                                <?endforeach;?>
                            <?endif;?>
                        </td>
                    <?endforeach;?>
                </tr>
            <?endforeach;?>
        </tbody>
    </table>

<?endif;?>