<script>
    $('.js-save-attendance').on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serializeObject();
        $.post('/attendance-school/save', data, function (html) {
            if (html) {
                $('#' + data['student_id'] + '-' + data['date']).empty().html(html);
            }
            $('#editAttendance').modal('hide');
        });
    });

    $('.js-attendance-modal').on('click', function () {
        var $container = $(this).find('.attendance-container');
        $.get(
            '/attendance-school/edit',
            {
                'attendance_id': $container.data('attendance'),
                'student_id': $container.data('student'),
                'date': $container.data('date'),
            },
            function (html) {
                $('#editAttendance .modal-content').html(html);
                eventsForModal();
                $('#editAttendance').modal('show');
            }
        );
    });

    function eventsForModal() {
        $('.js-attendance-delete').off().on('click', function () {
            if (confirm('Вы точно хотите удалить эту запись?')) {
                var self = $(this),
                    data = {
                    'attendance_id': $(this).data('attendance'),
                    'student_id': $(this).data('student')
                };
                $.post('/attendance-school/delete', data, function (answer) {
                    if (1 === answer.res) {
                        $('#' + data['student_id'] + '-' + self.data('date')).empty();
                    }
                    $('#editAttendance').modal('hide');
                });
            }
        });
    }
</script>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/attendance/scripts.blade.php ENDPATH**/ ?>