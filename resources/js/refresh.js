function newData() {
    document.querySelector("#dataRefresh").innerHTML =
        '<div class="spinner-border spinner-border-sm text-primary"></div>';

    axios
        .get("bonnen/cache")
        .then(function(response) {
            toastr.success(response.data, "Gelukt!");

            toastr.info(
                "Alle gegevens van de diverse BV's worden vernieuwd. Het laden van de nieuwe data neemt even wat tijd in beslag.",
                "Data"
            );
            document.querySelector("#dataRefresh").innerHTML =
                '<i class="fa fa-refresh text-primary" aria-hidden="true" style="cursor:pointer;" title="Data refreshen"></i>';
            setTimeout(function() {
                location.reload(true);
            }, 5000);
        })
        .catch(function(error) {
            toastr.error(error, "Er is iets niet goed gegaan!");
        });
}
