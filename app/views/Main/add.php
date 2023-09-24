<div class="container mt-5">
    <h2 class="text-center">Добавление</h2>
    <form id="addForm" action="/main/add" method="POST">
        <div class="form-group">
            <label for="itemName">Введите название:</label>
            <input type="text" id="itemName" name="name" class="form-control" required value="<?= $name ?? '' ?>">
        </div>
        <input type="hidden" id="itemId" name="pid" value="<?= $id ?? 0 ?>">
        <button type="submit" class="btn btn-primary">Добавить</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href = '/'">Назад</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $("#addForm").on("submit", function(event) {
            event.preventDefault();
            const form = $(this);

            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                success: function(response) {
                    window.location.href = "/";
                },
                error: function() {
                    alert("Произошла ошибка при обращении к серверу.");
                },
            });
        });
    });
</script>