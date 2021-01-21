async function initialPayment(id, amount, internNummer) {
    try {
        document.getElementById(id).innerHTML =
            "<div id='spinner-" +
            id +
            "' class='spinner-border spinner-border-sm'></div> Moment..";
        let response = await axios.post("initial-payment", {id, amount, internNummer});
        toastr.success(
            "Initiele betaling geregistreerd!",
            "Factuur afgevinkt"
        );
        document.getElementById(id).innerHTML = 'Klaar';
        document.getElementById(id).disabled = true;
    } catch (err) {
        toastr.error(err, "Initiele betaling niet geregistreerd!");
        document.getElementById(id).innerHTML = '€' + amount;
        document.getElementById(id).disabled = False;
    }
}

async function restPayment(id, amount) {
    try {
        document.getElementById(id).innerHTML =
            "<div id='spinner-" +
            id +
            "' class='spinner-border spinner-border-sm'></div> Moment..";
        let response = await axios.post("rest-payment", {id, amount});
        toastr.success(
            "Restant betaling geregistreerd! Factuur is nu volledig verwerkt.",
            "Factuur verwerkt"
        );
        document.getElementById(id).innerHTML = 'Klaar';
        document.getElementById(id).disabled = true;
    } catch (err) {
        toastr.error(err, "Restant betaling niet geregistreerd!");
    }
}


let data ={
    numbers:{
        amount: []
    },
    totals:{
        amount: 0
    }
}

function countTotals(){
    let totalAmount = 0;
    for(let i = 0; i < data.numbers.amount.length; i++){
        totalAmount += data.numbers.amount[i];
    }

    data.totals.amount = totalAmount;
}

function updateCounter(amount) {
    data.numbers.amount.push(amount);
    countTotals();
}

$("input:checkbox").on("change", function () {
    let amount = parseFloat($(this).closest('tr').find('.count').text().replace(',', '.').replace('€', ''));
    if ($(this).prop('checked')===false) {
        amount = -amount;
    }

    updateCounter(amount);

    document.querySelector('.amount').textContent = new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(data.totals.amount);
});

function deleteInitial(id){
    if(confirm("Factuur van de factoringlijst afhalen?")){
        try{
            axios.get("initial-payment/delete/"+id);
            toastr.success('Factuur verwijderd van lijst.');
            document.getElementById("row-"+id).closest("tr").remove();
        } catch(e) {
            toastr.error(e.response);
        }
    }
}
