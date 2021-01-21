async function postAll(e,t,r){await addOrder(e,t),await VapFactuur(e,r),await markReceipt(e)}async function addOrder(e,t){try{await axios.post("addOrder",{id:e,data:t});toastr.success("Factuur is aangemaakt in Snelstart!","Factuur aangemaakt"),document.querySelector("#submit-"+e).innerHTML="<div id='spinner-"+e+"' class='spinner-border spinner-border-sm'></div> Factuur aan VAP toevoegen.."}catch(t){toastr.error(t,"Factuur niet geimporteerd in SnelStart!"),document.querySelector("#submit-"+e).innerHTML="Verkoopfactuur maken van bovenstaande bon",document.querySelector("#submit-"+e).classList.remove("disabled")}}async function VapFactuur(e,t){try{await axios.post("VapFactuur",t);toastr.success("Factuur staat in VAP!","Factuur aangemaakt"),document.querySelector("#submit-"+e).innerHTML="<div id='spinner-"+e+"' class='spinner-border spinner-border-sm'></div> Bon markeren als gereed.."}catch(t){toastr.error(t,"Factuur niet aangemaakt in VAP!"),document.querySelector("#submit-"+e).innerHTML="Verkoopfactuur maken van bovenstaande bon",document.querySelector("#submit-"+e).classList.remove("disabled")}}async function markReceipt(e){try{await axios.post("markReceipt",{id:e});toastr.success("Bon als verwerkt gemarkeerd!","Markeren"),document.querySelector("#submit-"+e).innerHTML="Verwerkt"}catch(t){toastr.error(t,"Bon niet verwerkt als gemarkeerd!"),document.querySelector("#submit-"+e).innerHTML="Verkoopfactuur maken van bovenstaande bon",document.querySelector("#submit-"+e).classList.remove("disabled")}}function makeInvoice(e){document.querySelector("#submit-"+e).innerHTML="<div id='spinner-"+e+"' class='spinner-border spinner-border-sm'></div> Factureren..",document.querySelector("#submit-"+e).classList.add("disabled"),document.querySelector("#submit-"+e).setAttribute("disabled","true"),document.querySelector("#delete-"+e).setAttribute("disabled","true");let t=document.querySelector("#project-"+e).value,r=document.querySelector("#date-"+e).value,n=document.querySelector("#termijn-"+e).value,o=document.querySelector("#select-"+e).value,a=document.querySelector("#artikel-"+e).value,u=document.querySelector("#sjabloon-"+e).value,i=document.querySelector("#toelichting-"+e).value,s=document.querySelector("#bedrag-"+e).value,c={projectId:t,fl:1,factuurbedrag:s,datum:r,omschrijving:i,krediettermijn:n},d={relatie:{id:o},VerkooporderBtwIngaveModel:2,procesSatus:"Order",datum:r,krediettermijn:n,omschrijving:"Concept factuur (via API)",regels:[{artikel:{id:a},stuksprijs:s,aantal:1,totaal:s},{stuksprijs:0,omschrijving:i}],verkoopordersjabloon:{id:u}};d=JSON.stringify(d),o&&u&&a&&n?postAll(e,d,c):u?o?a?n||(toastr.error("Graag het termijn in dagen in te vullen!.","Betalingstermijn niet ingevuld!"),btnBehavior(e,"termijn")):(toastr.error("Geen artikel geselecteerd!.","Artikel niet gevonden!"),btnBehavior(e,"artikel")):(toastr.error("Klant vanuit Snelstart moet gekoppeld worden met een klant uit VAP.","Klant niet gevonden!"),btnBehavior(e,"select")):(toastr.error("Geen sjabloon gekozen voor de factuur.","Sjabloon niet gevonden!"),btnBehavior(e,"sjabloon"))}function btnBehavior(e,t){document.querySelector("#"+t+"-"+e).focus(),document.querySelector("#submit-"+e).removeAttribute("disabled"),document.querySelector("#submit-"+e).classList.remove("disabled"),document.querySelector("#submit-"+e).innerHTML="Verkoopfactuur maken van bovenstaande bon",document.querySelector("#delete-"+e).removeAttribute("disabled")}function deleteBon(e){axios.delete("factuur/"+e).then(function(t){toastr.info("Bon gemarkeerd met status 'gefactureerd'","Bon verwerkt"),document.getElementById("row-"+e).remove(),document.getElementById("hr-"+e).remove()}).catch(function(e){console.log(e),toastr.error(e,"Bon niet verwerkt!")})}