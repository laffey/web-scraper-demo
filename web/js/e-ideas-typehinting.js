/**
 * Created by elaffey on 4/16/15.
 */
$(document).ready(function () {
    $.ajax({
        url: "/api/search-hints",
        cache: false
    }).done(function (response) {
        if (typeof response.search_hints === 'object') {
            var engine = new Bloodhound({
                name: 'type_hinting',
                local: response.search_hints,
                datumTokenizer: function(d) {
                    return Bloodhound.tokenizers.whitespace(d.val);
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });
            engine.initialize();
            $('.typeahead').typeahead(null, {
                displayKey: 'val',
                source: engine.ttAdapter()
            })
            .on('typeahead:selected', function ($e, suggestion, dataset) {
                openPayments.filterResults(suggestion.val, suggestion.category, 1);
            })
            .on('typeahead:autocompleted', function ($e, suggestion, dataset) {
                openPayments.filterResults(suggestion.val, suggestion.category, 1);
            });
        }
    });

    openPayments.getPayments(1);

    $('.get-more-button').each(function() {
        $(this).click(function () {
            openPayments.nextPage();
        });
    });
});

var openPayments = {
    page: 1,
    recordsLoaded: 0,
    domTableWrapper: '#payments_table_wrapper',
    domTable: 'payments_table',
    domLoaderIcon: '#loader-wrapper',
    domWarningBox: '#result-warning',
    domDangerBox: '#result-danger',
    domRecordCount: '#record-count',
    filterCategory: null,
    filterValue: null,
    hasMore: true,
    getPayments: function(page) {
        var self = this;
        if (page == 1) {
            self.recordsLoaded = 0;
        }
        $(self.domLoaderIcon).show();
        $(self.domWarningBox).hide();
        $(self.domDangerBox).hide();
        if (self.hasMore) {
            $.ajax({
                url: "/api/payments/" + page,
                cache: false
            }).error(function() {
                $(self.domDangerBox).show();
            }).done(function(response) {
                self.buildTable(response);
            });
        } else {
            $(self.domWarningBox).show();
            $(self.domLoaderIcon).hide();
        }
    },
    buildTable: function(response) {
        var self = this;
        if (typeof response.count !== 'undefined' && typeof response.per_page !== 'undefined') {
            if (response.count < response.per_page) {
                this.hasMore = false;
            }
            self.displayRecordCount(response.count, response.total_records);
            if (response.count == 0) {
                $(self.domLoaderIcon).hide();
                $(self.domWarningBox).show();
                return;
            }
        }
        if (typeof response.page !== 'undefined') {
            this.page = response.page;
        }
        if (typeof response.payments === 'object' && typeof response.column_headers === 'object') {
            //build a table
            var tableWrapper = $('#' + self.domTable);
            var appendNeeded = false;
            if (tableWrapper.length == 0) {
                tableWrapper = $('<table class="table" id="' + self.domTable + '"></table>');
                appendNeeded = true;
            }
            //check if we need to build header row
            if (tableWrapper.find('tr th').length == 0) {
                var headerRow = $('<tr></tr>');
                for (var columnId in response.column_headers) {
                    if (!response.column_headers.hasOwnProperty(columnId)) continue;
                    var th = $('<th></th>').append(document.createTextNode(response.column_headers[columnId]));
                    headerRow.append(th);
                }
                tableWrapper.append(headerRow);
            }
            for (var recordId in response.payments) {
                if (!response.payments.hasOwnProperty(recordId)) continue;
                if (typeof response.payments[recordId] == 'object') {
                    var record = response.payments[recordId];
                    var row = $('<tr></tr>');
                    //loop known columns, so that the row matches the header
                    for (var columnId in response.column_headers) {
                        if (!response.column_headers.hasOwnProperty(columnId)) continue;
                        var columnText = '&nbsp;';
                        if (!record.hasOwnProperty(columnId)) {
                            //check arbitrary columns
                            if (record.hasOwnProperty('misc_columns')) {
                                if (record['misc_columns'].hasOwnProperty(columnId)) {
                                    if (record['misc_columns'][columnId] != null) {
                                        columnText = document.createTextNode(record['misc_columns'][columnId]);
                                    }
                                }
                            }
                        } else {
                            if (record[columnId] != null) {
                                columnText = document.createTextNode(record[columnId]);
                            }
                        }
                        var td = $('<td></td>').append(columnText);
                        row.append(td);
                    }
                    tableWrapper.append(row);
                }
            }
            if (appendNeeded) {
                $(this.domTableWrapper).empty();
                $(this.domTableWrapper).append(tableWrapper);
            }
        }
        $(self.domLoaderIcon).hide();
    },
    nextPage: function() {
        if (this.filterCategory !== null && this.filterValue !== null) {
            if (!this.hasMore) {
                $(this.domWarningBox).show();
                return;
            }
            this.filterResults(this.filterValue, this.filterCategory, parseInt(this.page) + 1);
        } else {
            this.getPayments(parseInt(this.page) + 1);
        }
    },
    filterResults: function(value, category, page) {
        var self = this;
        $(self.domLoaderIcon).show();
        $(self.domWarningBox).hide();
        $(self.domDangerBox).hide();
        if (page == 1) {
            self.hasMore = true;
            self.recordsLoaded = 0;
            $(this.domTableWrapper).empty();
        }
        self.filterValue = value;
        self.filterCategory = category;
        $.ajax({
            url: "/api/payments/filter/" + encodeURIComponent(value) + '/' + encodeURIComponent(category) + '/' + page,
            cache: false
        }).fail(function() {
            //always will handle this
        }).always(function(response, status, errorThrown) {
            if (typeof response.responseJSON != 'undefined' && typeof response.responseJSON.result !== 'undefined') {
                $(self.domWarningBox).show();
            } else if (status == 'error') {
                $(self.domDangerBox).show();
            }
            $(self.domLoaderIcon).hide();
        }).done(function(response) {
            self.buildTable(response);
        });
    },
    displayRecordCount: function(count, total_records) {
        var self = this;
        var recordCountWrapper = $(self.domRecordCount);
        if (total_records == 0) {
            recordCountWrapper.html(
                '0 records'
            );
        } else {
            self.recordsLoaded += count;
            recordCountWrapper.html(
                self.recordsLoaded + ' / <span class="badge">' + total_records + '</span>'
            );
        }
    }
};