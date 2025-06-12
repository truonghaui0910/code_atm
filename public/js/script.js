//ham ve flot-chart 
drawChart = function (selector, ticks, labels, datas) {
    var data = [{
            label: labels,
            data: datas,
            lines: {
                show: true,
                fill: false
            },
            points: {
                show: true
            }
        }];
    var options = {
        series: {
            shadowSize: 0
        },
        grid: {
            hoverable: true,
            clickable: true,
            tickColor: "transparent",
            borderWidth: 0
        },
        colors: ["#3bafda", "#f76397", "#34d3eb"],
        tooltip: true,
        tooltipOpts: {
            defaultTheme: false,
            //            content: "%s : %y.0"
            content: "%y.0"
        },
        legend: {
            position: "ne",
            margin: [0, -24],
            noColumns: 0,
            labelBoxBorderColor: null,
            labelFormatter: function (label, series) {
                // just add some space to labes
                return '' + label + '&nbsp;&nbsp;';
            },
            width: 30,
            height: 2
        },
        yaxis: {
            //            show: false,
            tickColor: '#f5f5f5',
            tickLength: 0,
            font: {
                color: '#bdbdbd'
            }
        },
        xaxis: {
            ticks: ticks,
            tickColor: '#f5f5f5',
            tickLength: 0,
            font: {
                color: '#bdbdbd'
            }
        }
    };

    $.plot($(selector), data, options);
};

drawLineChart = function (chartId, dataView, labelView, label) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelView,
            datasets: [{
                    label: label,
                    data: dataView,
                    fill: false,
                    backgroundColor: 'rgb(25, 165, 253)',
                    borderColor: 'rgb(25, 165, 253)',
                    borderWidth: 1
                }]
        },
        options: {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, ',', '.');
                        return label;
                    }
                }
            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: true,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }, gridLines: {
                            display: true
                        }
                    }], xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0
                }
            }
        }
    });
};

drawLineChartMini = function (chartId, dataView, labelView, label) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelView,
            datasets: [{
                    label: label,
                    data: dataView,
                    fill: false,
                    backgroundColor: 'rgb(25, 165, 253)',
                    borderColor: 'rgb(25, 165, 253)',
                    borderWidth: 1
                }]
        },
        options: {
            responsive: true,
            tooltips: {
                //                displayColors: false,
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, ',', '.');
                        return label;
                    }
                }
            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: false
                        }, gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }],
                xAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: false
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }]
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0,
                    borderWidth: 1
                }, point: {
                    pointStyle: 'circle',
                    radius: 1

                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            bezierCurve: false, //remove curves from your plot
            scaleShowLabels: false, //remove labels
            tooltipEvents: [], //remove trigger from tooltips so they will'nt be show
            pointDot: false, //remove the points markers
            scaleShowGridLines: true, //set to false to remove the grids background
            //ẩn chú thích màu
            legend: {
                display: false
            }
        }
    });
    myChart.canvas.parentNode.style.height = '75px';
    myChart.canvas.parentNode.style.width = '170px';
    //    myChart.defaults.plugins.legend.display = false;
    //myChart.defaults.elements.point.borderWidth= 1;
};

drawLineCharts = function (chartId, labelView, datasets) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelView,
            datasets: datasets
        },
        options: {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, ',', '.');
                        if (typeof data.datasets[tooltipItem.datasetIndex].wake !== 'undefined') {
                            label += ' ' + data.datasets[tooltipItem.datasetIndex].wake[tooltipItem.index];
                        }
                        return label;
                    }, footer: function (tooltipItem, data) {
                        //hiển thị tooltips xuống dòng dữ liệu kiểu ["string1","string2","string3"]
                        if (typeof data.datasets[tooltipItem[0].datasetIndex].footer !== 'undefined') {
                            return data.datasets[tooltipItem[0].datasetIndex].footer[tooltipItem[0].index];
                        }
                    }
                },
                footerFontStyle: 'normal',
                footerFontSize: 11

            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: true,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }, gridLines: {
                            display: true
                        }
                    }], xAxes: [{
                        type: 'time',
                        distribution: 'series',
                        time: {
                            unit: 'day',
                            displayFormats: {
                                day: 'YYYY/MM/DD'
                            }
                        },
                        display: true,
                        gridLines: {
                            display: false
                        }
                    }]
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0
                }
            }
        }
    });
};

drawChart = function (lineChartData, typeChart) {
    var ctx = document.getElementById('myChart1').getContext('2d');
    myChart = new Chart(ctx, {
        type: typeChart,
        data: lineChartData,
        options: {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, ',', '.');
                        return label;
                    }
                }
            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: false
                        }, gridLines: {
                            display: true
                        }
                    }], xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
            }
        }
    });
};

drawBarChart = function (chartId, descritpion, labels, datas) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: descritpion,
                    data: datas,
                    fill: false,
                    backgroundColor: '#2fa5cb',
                    borderColor: '#2fa5cb',
                    borderWidth: 1
                }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }, gridLines: {
                            display: true
                        }
                    }],
                xAxes: [{
                        categoryPercentage: 0.96,
                        barPercentage: 0.96,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: false //this will remove only the label
                        }
                    }]
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = number_format(tooltipItem.yLabel, 0, ',', '.');
                        return label;
                    }
                }

            }
        }
    });
};

drawBarChartsGroup = function (chartId, labelView, datasets) {
    console.log('datasets', datasets);
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelView,
            datasets: datasets
        },
        options: {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += '$' + number_format(Math.round(tooltipItem.yLabel * 100) / 100, 0, '.', ',');

                        return label;
                    }, footer: function (tooltipItem, data) {
                        //hiển thị tooltips xuống dòng dữ liệu kiểu ["string1","string2","string3"]
                        if (typeof data.datasets[tooltipItem[0].datasetIndex].footer !== 'undefined') {
                            return data.datasets[tooltipItem[0].datasetIndex].footer[tooltipItem[0].index];
                        }
                    }
                },
                footerFontStyle: 'normal',
                footerFontSize: 11

            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            display: true,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return datasets[0].currency + beautyNumber(value);
                                }
                            }
                        }, gridLines: {
                            display: false
                        }
                    }], xAxes: [{
                        categoryPercentage: 0.96,
                        barPercentage: 0.96,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: true //this will remove only the label
                        }
                    }]
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0
                }
            }
        }
    });
};

drawBarChartMini = function (chartId, descritpion, labels, datas, heightRatio = 100) {
//    heightRatio để biểu diễn height của cột ở các biểu đồ khác nhau, lấy số view lớn nhất làm chuẩn. 100% = 50px
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: descritpion,
                    data: datas,
                    fill: false,
                    backgroundColor: '#2fa5cb',
                    borderColor: '#2fa5cb',
                    borderWidth: 1
                }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                yAxes: [{
                        ticks: {
                            display: false
                        },
                        gridLines: {
                            display: false, drawBorder: false
                        }
                    }],
                xAxes: [{
                        barPercentage: 1,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: false //this will remove only the label
                        }
                    }]
            },
            legend: {
                display: false
            },
            tooltips: {
                enabled: false
            },
            bezierCurve: false, //remove curves from your plot
            scaleShowLabels: false, //remove labels
            tooltipEvents: [], //remove trigger from tooltips so they will'nt be show
            pointDot: false, //remove the points markers
            scaleShowGridLines: true //set to false to remove the grids background
        }
    });
    var height = 60;
    myChart.canvas.parentNode.style.height = (height * heightRatio / 100) + 'px';
    myChart.canvas.parentNode.style.width = '170px';
};

drawBarCharts = function (chartId, descritpion, labels, datas) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: descritpion,
                    data: datas,
                    fill: false,
                    backgroundColor: '#2fa5cb',
                    borderColor: '#2fa5cb',
                    borderWidth: 1
                }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }, gridLines: {
                            display: true
                        }
                    }],
                xAxes: [{
                        categoryPercentage: 0.96,
                        barPercentage: 0.96,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: false //this will remove only the label
                        }
                    }]
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = number_format(tooltipItem.yLabel, 0, ',', '.');
                        return label;
                    }
                }

            }
        }
    });
};

drawPieChart = function (chartId, labels, datasets) {
    var ctx = document.getElementById(chartId).getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        console.log(data, tooltipItem);
                        var label = data.labels[tooltipItem.index] || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '$' + number_format(Math.round(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] * 100) / 100, 0, '.', ',');
                        return label;
                    }
                }
            },
            responsive: true,
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    generateLabels: function (chart) {
                        var data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map(function (label, i) {
                                var meta = chart.getDatasetMeta(0);
                                var ds = data.datasets[0];
                                var arc = meta.data[i];
                                var custom = arc && arc.custom || {};
                                var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
                                var arcOpts = chart.options.elements.arc;
                                var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                                var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
                                var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);
                                var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];
                                value = number_format(Math.round(value * 100) / 100, 0, '.', ',')
                                return {
                                    text: label + " : $" + value,
                                    fillStyle: fill,
                                    strokeStyle: stroke,
                                    lineWidth: bw,
                                    hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                    index: i
                                };
                            });
                        } else {
                            return [];
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Income by source'
                }
            }
        }
    });
};

function number_format(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function getTIMESTAMP(time) {
    if (time == 0) {
        var date = new Date();
    } else {
        var date = new Date(time * 1000);
    }
    var year = date.getFullYear();
    var month = ("0" + (date.getMonth() + 1)).substr(-2);
    var day = ("0" + date.getDate()).substr(-2);
    var hour = ("0" + date.getHours()).substr(-2);
    var minutes = ("0" + date.getMinutes()).substr(-2);
    var seconds = ("0" + date.getSeconds()).substr(-2);
    return month + "/" + day + "/" + year;
}

notify = function (message, link, icon) {
    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    } else {
        var notification = new Notification('Automusic', {
            icon: icon,
            body: message
        });
        notification.onclick = function () {
            window.open(link);
        };
    }
};

function shortNumber2Number(shortNumber) {
    if (shortNumber == '' || shortNumber == null) {
        return 0;
    }
    if (shortNumber.includes("K")) {
        return parseFloat(shortNumber) * 1000;
    } else if (shortNumber.includes("M")) {
        return parseFloat(shortNumber) * 1000000;
    } else if (shortNumber.includes("B")) {
        return parseFloat(shortNumber) * 1000000000;
    } else if (shortNumber.includes("T")) {
        return parseFloat(shortNumber) * 1000000000000;
    } else {
        return parseFloat(shortNumber);
    }
}

function number2shortNumber(num) {
    let formatter = Intl.NumberFormat('en', {notation: 'compact'});
    return formatter.format(num);
    //    return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'K' : Math.sign(num)*Math.abs(num);
}

validateInputTarget = function (evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var codes = [75, 107, 77, 109, 66, 98, 46];
    if ($.inArray(charCode, codes) != -1) {
        return true;
    }
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        $.Notification.autoHideNotify('error', 'top center', 'Notify', 'You can only enter digits and the letters K, M, B');
        return false;
    }
    return true;
}

function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaeeeeiiiioooouuuunc------";
    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '_') // collapse whitespace and replace by -
            .replace(/-+/g, '_'); // collapse dashes

    return str;
}

function beautyNumber(number) {
    return number_format(Math.round(number * 100) / 100, 0, '.', ',');
}

logger = function (name, value = "") {
    if (localStorage.debug == 1) {
        console.log(name, value);
}
};

removeItemOnce = function (arr, value) {
    var index = arr.indexOf(value);
    if (index > -1) {
        arr.splice(index, 1);
    }
    return arr;
};

const formatDateToGMT7 = (date) => {
  // Lấy múi giờ GMT+7 bằng cách thêm 7 giờ vào đối tượng Date
  const gmt7Date = new Date((date + 7 * 60 * 60) * 1000);

  // Lấy các thành phần của ngày
  const year = gmt7Date.getUTCFullYear();
  const month = String(gmt7Date.getUTCMonth() + 1).padStart(2, '0');
  const day = String(gmt7Date.getUTCDate()).padStart(2, '0');

  // Lấy các thành phần của giờ
  const hours = String(gmt7Date.getUTCHours()).padStart(2, '0');
  const minutes = String(gmt7Date.getUTCMinutes()).padStart(2, '0');
  const seconds = String(gmt7Date.getUTCSeconds()).padStart(2, '0');

  // Trả về chuỗi ngày tháng định dạng Y/m/d H:i:s
  return `${year}/${month}/${day} ${hours}:${minutes}:${seconds}`;
};

function btoaUnicode(str) {
  // Chuyển chuỗi Unicode thành chuỗi Latin1 an toàn để dùng với btoa
  return btoa(unescape(encodeURIComponent(str)));
}

function atobUnicode(str) {
  // Giải mã Base64 và chuyển chuỗi Latin1 về Unicode
  return decodeURIComponent(escape(atob(str)));
}




function showRewardNotification(options) {
  // Kiểm tra jQuery đã được tải chưa
  if (typeof jQuery === 'undefined') {
    console.error('jQuery chưa được tải!');
    return;
  }

  // Xác định theme
  const theme = options.theme || 'gold';
  let themeColors = {};
  
  switch(theme) {
    case 'royal':
      themeColors = {
        primary: 'linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%)',
        secondary: '#4a00e0',
        accent: '#e5b2ff',
        text: '#2e0064',
        starColor: '#e5b2ff'
      };
      break;
    case 'neon':
      themeColors = {
        primary: 'linear-gradient(135deg, #fc00ff 0%, #00dbde 100%)',
        secondary: '#00dbde',
        accent: '#fc00ff',
        text: '#003b3c',
        starColor: '#fcff4f'
      };
      break;
    case 'rainbow':
      themeColors = {
        primary: 'linear-gradient(135deg, #ff0000 0%, #ff7f00 10%, #ffff00 20%, #00ff00 35%, #0000ff 50%, #4b0082 65%, #9400d3 80%)',
        secondary: '#ff4500',
        accent: '#ffff00',
        text: '#000000',
        starColor: '#ffff00'
      };
      break;
    case 'emerald':
      themeColors = {
        primary: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
        secondary: '#11998e',
        accent: '#38ef7d',
        text: '#004d40',
        starColor: '#b2ff59'
      };
      break;
    case 'sunrise':
      themeColors = {
        primary: 'linear-gradient(135deg, #ff512f 0%, #f09819 100%)',
        secondary: '#ff512f',
        accent: '#f09819',
        text: '#4a2700',
        starColor: '#ffe082'
      };
      break;
    case 'ocean':
      themeColors = {
        primary: 'linear-gradient(135deg, #1cb5e0 0%, #000851 100%)',
        secondary: '#1cb5e0',
        accent: '#00b0ff',
        text: '#000851',
        starColor: '#80d8ff'
      };
      break;
    case 'crimson':
      themeColors = {
        primary: 'linear-gradient(135deg, #8e0000 0%, #cc0000 100%)',
        secondary: '#8e0000',
        accent: '#ff5252',
        text: '#3e0000',
        starColor: '#ffcdd2'
      };
      break;
    case 'galaxy':
      themeColors = {
        primary: 'linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%)',
        secondary: '#302b63',
        accent: '#6f74dd',
        text: '#e0e0e0',
        starColor: '#c5cae9'
      };
      break;
    case 'gold':
    default:
      themeColors = {
        primary: 'linear-gradient(135deg, #ffd700 0%, #ffb700 100%)',
        secondary: '#ffd700',
        accent: '#ffb700',
        text: '#5d4c0e',
        starColor: '#ffd700'
      };
  }

  // CSS cho modal
  const modalCSS = `
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
      
      .reward-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.5s ease;
        backdrop-filter: blur(5px);
      }
      
      .reward-modal {
        background: linear-gradient(135deg, #f8f8f8 0%, #e8e8e8 100%);
        border-radius: 20px;
        width: 90%;
        max-width: 550px;
        padding: 0;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 215, 0, 0.2);
        transform: scale(0.7) translateY(30px);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
        font-family: 'Montserrat', sans-serif;
      }
      
      .reward-light-rays {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: 
          radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.3) 0%, transparent 25%),
          radial-gradient(circle at 80% 30%, rgba(255, 255, 255, 0.3) 0%, transparent 25%),
          radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.3) 0%, transparent 25%),
          radial-gradient(circle at 85% 70%, rgba(255, 255, 255, 0.3) 0%, transparent 25%);
        pointer-events: none;
        z-index: 1;
      }
      
      .reward-modal-header {
        background: ${themeColors.primary};
        color: white;
        padding: 20px 25px;
        text-align: center;
        position: relative;
        overflow: hidden;
      }
      
      .reward-modal-header::before, 
      .reward-modal-header::after {
        content: "";
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
        animation: pulse 3s infinite linear;
        pointer-events: none;
      }
      
      .reward-modal-header::after {
        animation-delay: 1.5s;
      }
      
      .reward-modal-title {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
      }
      
      .reward-modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 22px;
        color: white;
        cursor: pointer;
        background: none;
        border: none;
        opacity: 0.8;
        transition: all 0.3s;
        z-index: 5;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
      }
      
      .reward-modal-close:hover {
        opacity: 1;
        transform: rotate(90deg);
        background: rgba(255,255,255,0.2);
      }
      
      .reward-modal-body {
        padding: 40px 30px;
        text-align: center;
        position: relative;
        z-index: 2;
      }
      
      .reward-glitter-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
      }
      
      .reward-glitter {
        position: absolute;
        width: 6px;
        height: 6px;
        background-color: ${themeColors.accent};
        border-radius: 50%;
        opacity: 0;
      }
      
      .reward-avatar-container {
        margin-bottom: 25px;
        position: relative;
        display: inline-block;
        z-index: 2;
      }
      
      .reward-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 
          0 10px 20px rgba(0, 0, 0, 0.15),
          0 0 0 10px rgba(255, 255, 255, 0.2);
        animation: pulse-avatar 3s infinite ease-in-out;
      }
      
      .reward-avatar-decoration {
        position: absolute;
        top: -15px;
        left: -15px;
        right: -15px;
        bottom: -15px;
        border-radius: 50%;
        border: 3px dashed ${themeColors.accent};
        animation: rotate 20s linear infinite;
        pointer-events: none;
      }
      
      .reward-avatar-decoration::before {
        content: "";
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border-radius: 50%;
        border: 2px dotted ${themeColors.accent};
        opacity: 0.5;
        animation: rotate-reverse 15s linear infinite;
      }
      
      .reward-avatar-aura {
        position: absolute;
        top: -25px;
        left: -25px;
        right: -25px;
        bottom: -25px;
        border-radius: 50%;
        background: radial-gradient(circle, ${themeColors.secondary}40 0%, transparent 70%);
        animation: pulse-slow 2s infinite alternate;
        pointer-events: none;
        z-index: -1;
      }
      
      .reward-avatar-star {
        position: absolute;
        color: ${themeColors.starColor};
        text-shadow: 0 0 10px ${themeColors.starColor}80, 0 0 20px ${themeColors.starColor}40;
        animation: twinkle-star 1.5s infinite alternate;
        z-index: 3;
        font-size: 28px;
      }
      
      .reward-avatar-star::after {
        content: "✦";
        position: absolute;
        top: 0;
        left: 0;
        color: white;
        opacity: 0.7;
        filter: blur(1px);
        animation: twinkle-star-after 2s infinite alternate-reverse;
      }
      
      .star-1 { top: -15px; right: 20px; animation-delay: 0s; font-size: 30px; }
      .star-2 { bottom: 0px; right: -5px; animation-delay: 0.3s; font-size: 22px; }
      .star-3 { bottom: 0px; left: -5px; animation-delay: 0.6s; font-size: 22px; }
      .star-4 { top: -15px; left: 20px; animation-delay: 0.9s; font-size: 30px; }
      .star-5 { top: 50%; left: -15px; animation-delay: 1.2s; font-size: 18px; }
      .star-6 { top: 50%; right: -15px; animation-delay: 1.5s; font-size: 18px; }
      
      .reward-medal {
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 40px;
        filter: drop-shadow(0 3px 5px rgba(0,0,0,0.3));
        z-index: 4;
      }
      
      .reward-name {
        margin-left:20px;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
        color: ${themeColors.text};
        position: relative;
        display: inline-block;
        text-shadow: 0 2px 5px rgba(0,0,0,0.1);
      }
      
      .reward-name::after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 3px;
        background: ${themeColors.primary};
        transform: scaleX(0);
        transform-origin: center;
        transition: transform 0.5s ease;
      }
      
      .reward-modal.active .reward-name::after {
        transform: scaleX(1);
      }
      
      .reward-position {
        font-size: 18px;
        color: ${themeColors.secondary}DD;
        margin-bottom: 30px;
        font-weight: 600;
        letter-spacing: 1px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s ease;
        transition-delay: 0.3s;
      }
      
      .reward-modal.active .reward-position {
        opacity: 1;
        transform: translateY(0);
      }
      
      .reward-spotlight {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(ellipse at center, rgba(255,255,255,0.2) 0%, transparent 60%);
        pointer-events: none;
        z-index: 1;
        opacity: 0;
        transition: opacity 1s ease;
      }
      
      .reward-modal.active .reward-spotlight {
        opacity: 1;
      }
      
      .reward-content-wrapper {
        position: relative;
        margin-top: 20px;
        transform: translateY(30px);
        opacity: 0;
        transition: all 0.6s ease;
        transition-delay: 0.6s;
      }
      
      .reward-modal.active .reward-content-wrapper {
        transform: translateY(0);
        opacity: 1;
      }
      
      .reward-content {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 25px;
        border-radius: 15px;
        font-size: 16px;
        line-height: 1.7;
        color: #333;
        position: relative;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255,255,255,0.3);
        overflow: hidden;
      }
      
      .reward-content::before {
        content: """;
        position: absolute;
        top: -20px;
        left: 10px;
        font-size: 100px;
        color: ${themeColors.secondary}30;
        font-family: serif;
        line-height: 1;
      }
      
      .reward-content::after {
        content: "";
        position: absolute;
        bottom: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: radial-gradient(circle, ${themeColors.secondary}20 0%, transparent 70%);
        border-radius: 50%;
      }
      
      .reward-footer {
        margin-top: 30px;
        padding-top: 15px;
        font-style: italic;
        color: #666;
        font-size: 14px;
        position: relative;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.7s ease;
        transition-delay: 0.9s;
      }
      
      .reward-modal.active .reward-footer {
        opacity: 1;
        transform: translateY(0);
      }
      
      .reward-footer::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 2px;
        background: ${themeColors.accent};
      }
      
      .reward-ribbon {
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        overflow: hidden;
        z-index: 3;
      }
      
      .reward-ribbon::before {
        content: "★";
        position: absolute;
        top: 25px;
        right: -20px;
        width: 150px;
        height: 35px;
        transform: rotate(45deg);
        background: ${themeColors.primary};
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
      }
      
      .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        opacity: 0;
        z-index: 9990;
      }
      
      .firework {
        position: fixed;
        opacity: 0;
        z-index: 9990;
        pointer-events: none;
      }
      
      @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.5); opacity: 0.2; }
        100% { transform: scale(1); opacity: 0.5; }
      }
      
      @keyframes pulse-slow {
        0% { opacity: 0.3; transform: scale(0.95); }
        100% { opacity: 0.7; transform: scale(1.05); }
      }
      
      @keyframes pulse-avatar {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
      }
      
      @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      
      @keyframes rotate-reverse {
        from { transform: rotate(360deg); }
        to { transform: rotate(0deg); }
      }
      
      @keyframes twinkle-star {
        from { opacity: 0.4; transform: scale(0.8); filter: blur(0px); }
        to { opacity: 1; transform: scale(1.1); filter: blur(0.5px); }
      }
      
      @keyframes twinkle-star-after {
        from { opacity: 0.2; transform: scale(1.2) translate(-1px, -1px); }
        to { opacity: 0.6; transform: scale(0.8) translate(1px, 1px); }
      }
      
      @keyframes fall {
        0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
        100% { transform: translateY(calc(100vh)) rotate(360deg); opacity: 0; }
      }
      
      @keyframes glitter {
        0% { transform: scale(0.2); opacity: 0; }
        50% { transform: scale(1); opacity: 1; }
        100% { transform: scale(0.2); opacity: 0; }
      }
      
      @keyframes firework {
        0% { transform: translate(var(--x), var(--y)) scale(0); opacity: 1; }
        50% { transform: translate(var(--x), var(--y)) scale(1); opacity: 1; }
        100% { transform: translate(var(--x), var(--y)) scale(1.2); opacity: 0; }
      }
      
      .shine-effect {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(
          90deg, 
          transparent, 
          rgba(255, 255, 255, 0.5), 
          transparent
        );
        transform: skewX(-20deg);
        animation: shine 3s infinite;
      }
      
      @keyframes shine {
        0% { left: -100%; }
        100% { left: 200%; }
      }
      
      .reward-badge {
        position: absolute;
        top: -20px;
        left: -20px;
        width: 70px;
        height: 70px;
        background: ${themeColors.secondary};
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 5;
        transform: rotate(-15deg);
      }
      
      .reward-badge::after {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        border: 2px dashed rgba(255,255,255,0.5);
        border-radius: 50%;
      }
    </style>
  `;

  // HTML của modal
  let badgeIcon = '🏆';
  
  // Chọn biểu tượng dựa trên theme
  if (theme === 'emerald') badgeIcon = '🌟';
  if (theme === 'ocean') badgeIcon = '🌊';
  if (theme === 'sunrise') badgeIcon = '🌅';
  if (theme === 'crimson') badgeIcon = '💎';
  if (theme === 'galaxy') badgeIcon = '🌌';
  
  const modalHTML = `
    <div class="reward-modal-overlay">
      <div class="reward-modal">

        <div class="reward-light-rays"></div>
        <div class="reward-modal-header">
          <h3 class="reward-modal-title">✨ Thông Báo Khen Thưởng ✨</h3>
          <div class="shine-effect"></div>
          <button class="reward-modal-close">&times;</button>
        </div>
        <div class="reward-modal-body">
          <div class="reward-spotlight"></div>
          <div class="reward-glitter-container"></div>
          
          <div class="reward-avatar-container">
            <div class="reward-avatar-aura"></div>
            <div class="reward-avatar-decoration"></div>
            <img class="reward-avatar" src="${options.avatar || ''}" alt="Avatar">
            <div class="reward-avatar-star star-1">✦</div>
            <div class="reward-avatar-star star-2">✦</div>
            <div class="reward-avatar-star star-3">✦</div>
            <div class="reward-avatar-star star-4">✦</div>
            <div class="reward-avatar-star star-5">✦</div>
            <div class="reward-avatar-star star-6">✦</div>
            <div class="reward-medal">🥇</div>
          </div>
          
          <h2 class="reward-name">${options.name || 'Nguyễn Văn A'}</h2>
          ${options.position ? `<div class="reward-position">${options.position}</div>` : ''}
          
          <div class="reward-content-wrapper">
            <div class="reward-content">
              ${options.content || 'Đã có những đóng góp xuất sắc cho công ty trong tháng này.'}
            </div>
          </div>
          
          <div class="reward-footer">
            Chúc mừng và cảm ơn vì những đóng góp quý báu của bạn!
          </div>
        </div>
      </div>
    </div>
  `;

  // Thêm CSS vào head
  if (!$('#reward-modal-styles').length) {
    $('head').append($(modalCSS).attr('id', 'reward-modal-styles'));
  }

  // Thêm modal vào body
  const $modal = $(modalHTML).appendTo('body');
  
  // Tạo hiệu ứng lấp lánh ngẫu nhiên
  function createGlitter() {
    const $glitterContainer = $modal.find('.reward-glitter-container');
    const glitterCount = 100;
    
    for (let i = 0; i < glitterCount; i++) {
      const $glitter = $('<div class="reward-glitter"></div>');
      const left = Math.random() * 100 + '%';
      const top = Math.random() * 100 + '%';
      const size = Math.random() * 4 + 2 + 'px';
      const delay = Math.random() * 5 + 's';
      const duration = Math.random() * 2 + 1 + 's';
      
      $glitter.css({
        left: left,
        top: top,
        width: size,
        height: size,
        animationDelay: delay,
        animationDuration: duration
      });
      
      $glitterContainer.append($glitter);
      
      // Kích hoạt animation
      setTimeout(() => {
        $glitter.css({
          animation: `glitter ${duration} infinite ease-in-out`
        });
      }, 100);
    }
  }
  
  // Tạo hiệu ứng pháo hoa
  function createFireworks() {
    const colors = [
      'radial-gradient(circle, #ff0000 10%, transparent 70%)',
      'radial-gradient(circle, #00ff00 10%, transparent 70%)',
      'radial-gradient(circle, #0000ff 10%, transparent 70%)',
      'radial-gradient(circle, #ffff00 10%, transparent 70%)',
      'radial-gradient(circle, #ff00ff 10%, transparent 70%)',
      'radial-gradient(circle, #00ffff 10%, transparent 70%)'
    ];
    
    for (let i = 0; i < 8; i++) {
      setTimeout(() => {
        const $firework = $('<div class="firework"></div>');
        const color = colors[Math.floor(Math.random() * colors.length)];
        const size = Math.random() * 100 + 50 + 'px';
        const x = Math.random() * 80 - 40 + '%';
        const y = Math.random() * 80 - 40 + '%';
        
        $firework.css({
          background: color,
          width: size,
          height: size,
          borderRadius: '50%',
          position: 'fixed',
          top: Math.random() * 70 + 15 + '%',
          left: Math.random() * 70 + 15 + '%',
          transform: 'translate(-50%, -50%)',
          animation: 'none',
          zIndex: 9990
        });
        
        $firework.css({
          '--x': x,
          '--y': y
        });
        
        $('body').append($firework);
        
        // Kích hoạt animation
        setTimeout(() => {
          $firework.css({
            animation: `firework ${Math.random() * 1 + 1}s forwards ease-out`
          });
          
          // Xóa pháo hoa sau khi animation kết thúc
          setTimeout(() => {
            $firework.remove();
          }, 2000);
        }, 100);
      }, i * 500);
    }
  }
  
  // Tạo hiệu ứng confetti
  function createConfetti() {
    const colors = ['#f44336', '#2196f3', '#ffeb3b', '#4caf50', '#9c27b0', '#ff9800', '#e91e63'];
    const shapes = ['circle', 'square', 'triangle'];
    const confettiCount = 150;
    
    for (let i = 0; i < confettiCount; i++) {
      const $confetti = $('<div class="confetti"></div>');
      const color = colors[Math.floor(Math.random() * colors.length)];
      const shape = shapes[Math.floor(Math.random() * shapes.length)];
      const left = Math.random() * 100 + '%';
      const size = Math.random() * 10 + 5 + 'px';
      const animationDuration = Math.random() * 3 + 2 + 's';
      const animationDelay = Math.random() * 1.5 + 's';
      
      let shapeCSS = '';
      if (shape === 'circle') {
        shapeCSS = 'border-radius: 50%;';
      } else if (shape === 'triangle') {
        shapeCSS = `
          clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
          border-radius: 0;
        `;
      }
      
      $confetti.css({
        backgroundColor: color,
        left: left,
        width: size,
        height: size,
        position: 'fixed',
        top: '-20px',
        zIndex: 9998,
        animation: `fall ${animationDuration} ease-in ${animationDelay} forwards`,
        boxShadow: '0 0 5px rgba(0,0,0,0.2)'
      });
      
      if (shape === 'circle') {
        $confetti.css('border-radius', '50%');
      } else if (shape === 'triangle') {
        $confetti.css({
          'clip-path': 'polygon(50% 0%, 0% 100%, 100% 100%)',
          'border-radius': '0'
        });
      }
      
      $('body').append($confetti);
      
      // Xóa confetti sau khi animation kết thúc
      setTimeout(() => {
        $confetti.remove();
      }, (parseFloat(animationDuration) + parseFloat(animationDelay)) * 1000);
    }
  }
  
  // Hiệu ứng hiển thị
  setTimeout(() => {
    $modal.css('opacity', '1');
    $modal.find('.reward-modal').css('transform', 'scale(1) translateY(0)');
    setTimeout(() => {
      $modal.find('.reward-modal').addClass('active');
      createGlitter();
      createConfetti();
      setTimeout(createFireworks, 500);
    }, 300);
  }, 100);

  // Xử lý đóng modal
  $modal.find('.reward-modal-close').on('click', function() {
    $modal.css('opacity', '0');
    $modal.find('.reward-modal').css('transform', 'scale(0.7) translateY(30px)');
    
    setTimeout(() => {
      $modal.remove();
      if (typeof options.onClose === 'function') {
        options.onClose();
      }
      
      // Xóa tất cả confetti và pháo hoa
      $('.confetti, .firework').remove();
    }, 500);
  });

  // Bắt sự kiện click outside
  $modal.on('click', function(e) {
    if ($(e.target).hasClass('reward-modal-overlay')) {
      $modal.find('.reward-modal-close').click();
    }
  });
}
