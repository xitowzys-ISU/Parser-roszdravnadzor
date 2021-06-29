<body>
<form name="ourForm" class="decor">
    <div class="form-left-decoration"></div>
    <div class="form-right-decoration"></div>
    <div class="circle"></div>
    <div class="form-inner">
        <h3>Выберите дату регистрации</h3>
        <label for="date-from"><h4>с</h4></label>
        <input type="date" class="registration_date-from" name="dateFrom">
        <h4>по</h4>
        <input type="date" class="registration_date-to" name="dateTo">
        <input type="submit" value="Дайте JSON">
    </div>

</form>

<p id="process">Выполнено: 0%</p>
<script>
    document.getElementsByClassName('registration_date-from').valueAsDate = new Date();
    document.getElementsByClassName('registration_date-to').valueAsDate = new Date();
</script>
</body>