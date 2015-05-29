$pp = jQuery.noConflict();

$pp(document).ready(function() {

    Number.prototype.formatMoney = function(c, d, t) {
        var n = this,
                c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;

        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    var getParcelsValue = function(value, interest, parcels) {
        interest = interest / 100;
        var E = 1.0;
        var cont = 1.0;

        for (var i = 1; i <= parcels; i++)
        {
            cont = cont * (interest + 1);
            E = E + cont;
        }
        
        E = E - cont;

        parcel = value * cont;
        return parcel / E;
    };

    var getPrice = function(value) {
        if (value > minParcel) {
            var finalText = '';
            var ppDecimalSymbol = optionsPrice.priceFormat.decimalSymbol;
            var ppCurrencyFormat = optionsPrice.priceFormat.pattern.replace('%s', '');
            var ppGroupSymbol = optionsPrice.priceFormat.groupSymbol;
            var finalText = '';
            var tableText = '';

            for (var i = 2; i <= maxNumberMonths; i++) {
                var parcel = 0;

                parcel = getParcelsValue(value, interest, i);

                if (parcel >= minParcel) {
                    var parcelToCurrency = ppCurrencyFormat + (parcel).formatMoney(2, ppDecimalSymbol, ppGroupSymbol);
                    finalText = ppText.replace('{preco}', parcelToCurrency);
                    finalText = finalText.replace('{parcelas}', i);

                    if (showTable) {
                        tableText += '<tr>';
                        tableText += '<td>' + ppTableText.replace('{parcelas}', i) + '</td>';
                        tableText += '<td>' + parcelToCurrency + '</td>';
                        tableText += '</tr>';
                    }
                }
            }

            $pp('.precoparcelado-parcels').html(finalText);
            $pp('.precoparcelado-table tbody').html(tableText);
        }

        return null;
    };

    var onPriceChange = function(e) {
        var price = optionsPrice.productPrice;

        // if is configurable product
        if (typeof spConfig !== 'undefined') {
            for (key in optionsPrice.optionPrices.config) {
                if (key == 'price') {
                    price += optionsPrice.optionPrices.config['price'];
                }
            }
        }
        else {
            for (key in optionsPrice.customPrices) {
                price += optionsPrice.customPrices[key].price;
            }
        }

        getPrice(price);
    };

    // add the 'change' event listener for each configurable option
    // if is configurable product
    if (typeof spConfig !== 'undefined') {
        for (var i = 0; i < spConfig.settings.length; i++) {
            $pp('#' + spConfig.settings[i].id).on('change', onPriceChange);
        }
    }
    else {
        for (id in optionsPrice.customPrices) {
            $pp('#' + id).on('change', onPriceChange);
        }
    }

});