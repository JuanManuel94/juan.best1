let table;
let table_name = "list-payments";
var noreceived_amount = 0,
  noreceived_total = 0;

document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Mis transacciones");

    table = $("#" + table_name).DataTable({
      ajax: {
        url: base_url + "/dashboard/list_paymentes",
        dataSrc: function (json) {
          // Filtrar los pagos de hoy
          let today = moment().format('YYYY-MM-DD'); // Fecha de hoy en formato YYYY-MM-DD
          return json.filter(function(payment) {
            // Filtrar pagos que tengan una payment_date igual a hoy
            return moment(payment.payment_date).format('YYYY-MM-DD') === today;
          });
        },
      },
      stripeClasses: ["stripe1", "stripe2"],
      deferRender: true,
      rowId: "id",
      columns: [
        { data: "internal_code", className: "text-center" },
        { data: "client" },
        { data: "invoice", className: "text-center" },
        {
          data: "payment_date",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY H:mm");
          },
        },
        { data: "bill_total" },
        { data: "amount_paid" },
        { data: "payment_type" },
        { data: "comment" },
        {
          data: "state",
          render: function (data, type, full, meta) {
            var state = "";
            if (data == 1) {
              state = '<span class="label label-success">RECIBIDO</span>';
            }
            return state;
          },
          className: "text-center",
        },
      ],
      drawCallback: function (settings) {
        var api = this.api().rows({ search: "applied" }).data();
        $.each(api, function (key, value) {
          noreceived_amount++;
          noreceived_total += parseFloat(value["count_total"]);
        });
      },
      initComplete: function (oSettings, json) {
        $("#" + table_name + "_wrapper div.container-options").html(
          '<div class="options-group btn-group m-r-5"><button type="button" class="btn btn-white"><span class="badge label-warning f-s-10 f-w-700 mr-1">' +
            noreceived_amount +
            "</span><span>" +
            currency_symbol +
            noreceived_total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
            "</span></button></div>"
        );
      },
    });
  },
  false
);
