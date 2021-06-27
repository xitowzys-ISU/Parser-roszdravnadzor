<body>
<form method="POST" action="form.html" class="decor">
    <div class="form-left-decoration"></div>
    <div class="form-right-decoration"></div>
    <div class="circle"></div>
    <div class="form-inner">
        <h3>Выберите дату регистрации</h3>
        <label for="date-from"><h4>с</h4></label>
        <input type="date" class="registration_date-from" name="date-from">
        <h4>по</h4>
        <input type="date" class="registration_date-to" name="date-to">
        <input type="submit" value="Дайте JSON">
    </div>
</form>
<script>
    document.getElementsByClassName('registration_date-from').valueAsDate = new Date();
    document.getElementsByClassName('registration_date-to').valueAsDate = new Date();
</script>
</body>