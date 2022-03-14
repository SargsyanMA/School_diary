<?php if($show_poll): ?>
    <!-- Modal -->
    <div class="modal fade" id="pollModal" tabindex="-1" role="dialog" aria-labelledby="pollModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Опрос</h4>
                </div>
                <form method="post" action="/poll/1/save-result">
                    <?php echo e(csrf_field()); ?>

                    <div class="modal-body">


                        <!--div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary" onclick="$('.js-poll-q2').removeClass('hidden').find('input').prop('required',true);">
                                <input type="radio" name="q1" value="Да" autocomplete="off" required > Да
                            </label>
                            <label class="btn btn-primary" onclick="$('.js-poll-q2').addClass('hidden').find('input').prop('required',false);">
                                <input type="radio" name="q1" value="Нет" autocomplete="off" required> Нет
                            </label>
                        </div>
                        <br/> <br/>

                        <div class="js-poll-q2 hidden">
                            <p>Актуальна ли для вас доставка?</p>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-primary">
                                    <input type="radio" name="q2" value="Да" autocomplete="off" required> Да
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" name="q2" value="Нет" autocomplete="off" required> Нет
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div-->
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#pollModal').modal('show');
        });
    </script>
<?php endif; ?>


<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/poll.blade.php ENDPATH**/ ?>