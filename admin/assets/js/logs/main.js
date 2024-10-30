import {Utils} from "../Utils.js";

const { __, _x, _n, _nx } = wp.i18n;

var table = undefined;

jQuery(document).ready(function ($) {
    $("#logs-file").on("change", onChangeLogs);
});

function onChangeLogs(event) {
    let file = jQuery(event.currentTarget).val();
    let level = jQuery(event.currentTarget).find("option:selected").data("level") ?? "";

    Utils.getLogsFile(file, level).then((responses) => {
        loadDataSet(responses);
    });
}

function loadDataSet(responses) {
    // Format data
    let dataSet = [];
    responses.forEach((json) => {
        let response = JSON.parse(json);

        let context = response.context ?? {};
        let post = context.post ?? {};

        let formatedDateTime = "";
        let dateTimeString = response.datetime ?? "";
        if (dateTimeString !== "") {
            let dateTime = new Date(dateTimeString);

            let year = dateTime.getFullYear();
            let month = String(dateTime.getMonth() + 1).padStart(2, '0');
            let day = String(dateTime.getDate()).padStart(2, '0');

            formatedDateTime = `${day}/${month}/${year} ${dateTime.getHours()}:${dateTime.getMinutes()}:${dateTime.getSeconds()}`
        }

        let data = {
            message: response.message ?? "",
            type: context.type ?? "",
            p_code: context.p_code ?? "",
            post_id: post.ID ?? "",
            lang: context.lang ?? "",
            date: formatedDateTime ?? "",
            context : JSON.stringify(context ?? {}, null, 2),
        }

        dataSet.push(data);
    });

    // Defining columns
    let columns = [
        {
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: '',
        },
        {
            data: 'message',
        },
        {
            data: 'type'
        },
        {
            data: 'p_code',
        },
        {
            data: 'post_id',
        },
        {
            data: 'lang'
        },
        {
            data: 'date'
        }
    ];

    // Generate table
    table = jQuery("#logs").DataTable({
        data: dataSet,
        columns: columns,
        ordering: false,
        destroy: true,
        pageLength: 50,
    });

    // Add event listener for opening and closing details
    jQuery("#logs tbody").off("click", "td.dt-control").on("click", "td.dt-control", (event) => {
        let tr = jQuery(event.currentTarget).closest('tr');
        let row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(formatSubDataSet(row.data())).show();
            tr.addClass('shown');
        }
    });
}

function formatSubDataSet(data) {
    // language=html
    return `
		<table>
			<tr>
				<td></td>
				<td><pre>${syntaxHighlight(data.context)}</pre></td>
			</tr>
		</table>
    `;
}

function syntaxHighlight(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
