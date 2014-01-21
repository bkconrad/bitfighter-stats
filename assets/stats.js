function mysqlTimeStampToDate(timestamp) {
    //input has to be in this format: 2007-06-05 15:26:02
    var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
  var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
  return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
}

// Assumes date_str is GMT
function humane_date(date_str){
  var time_formats = [
    [60, 'Just Now'],
    [90, '1 Minute'], // 60*1.5
    [3600, 'Minutes', 60], // 60*60, 60
    [5400, '1 Hour'], // 60*60*1.5
    [86400, 'Hours', 3600], // 60*60*24, 60*60
    [129600, '1 Day'], // 60*60*24*1.5
    [604800, 'Days', 86400], // 60*60*24*7, 60*60*24
    [907200, '1 Week'], // 60*60*24*7*1.5
    [2628000, 'Weeks', 604800], // 60*60*24*(365/12), 60*60*24*7
    [3942000, '1 Month'], // 60*60*24*(365/12)*1.5
    [31536000, 'Months', 2628000], // 60*60*24*365, 60*60*24*(365/12)
    [47304000, '1 Year'], // 60*60*24*365*1.5
    [3153600000, 'Years', 31536000], // 60*60*24*365*100, 60*60*24*365
    [4730400000, '1 Century'], // 60*60*24*365*100*1.5
    ];

  var dt = new Date
    , seconds = ((dt - new Date(date_str) + (dt.getTimezoneOffset() * 60000)) / 1000)
    , token = ' Ago'
    , i = 0
    , format
    ;

  if (seconds < 0) {
    seconds = Math.abs(seconds);
    token = '';
  }

  while (format = time_formats[i++]) {
    if (seconds < format[0]) {
      if (format.length == 2) {
        return format[1] + (i > 1 ? token : ''); // Conditional so we don't return Just Now Ago
      } else {
        return Math.round(seconds / format[2]) + ' ' + format[1] + (i > 1 ? token : '');
      }
    }
  }

  // overflow for centuries
  if(seconds > 4730400000)
    return Math.round(seconds / 4730400000) + ' Centuries' + token;

  return date_str;
};

window.onload = function() {
  // hide advanced search
  document.getElementById('advanced-search').style.display = "none";

  // add js-related elements
  var advancedSearchLink = document.createElement('a');
  advancedSearchLink.innerHTML = 'Show advanced search options';
  advancedSearchLink.href = '#';
  advancedSearchLink.onclick = function () {
    var element = document.getElementById('advanced-search');
    if (element.style.display == 'none') {
      advancedSearchLink.innerHTML = 'Hide advanced search options';
      element.style.display = 'block';
    } else {
      advancedSearchLink.innerHTML = 'Show advanced search options';
      element.style.display = 'none';
    }
    return false;
  };
  document.getElementById('search-form').appendChild(advancedSearchLink);

  var offsetFromGMT = 7; // TODO this is a dummy value
  var cells = document.getElementsByClassName('last-seen');
  var i;
  var cellDate;
  for (i = 0; i < cells.length; i++) {
    cellDate = mysqlTimeStampToDate(cells[i].innerHTML);
    cells[i].innerHTML = humane_date(cellDate.getTime() - offsetFromGMT * 1000);
  }
};
