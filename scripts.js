// RadioPanel Scripts

$(document).ready(function(e) {
	
	// Week view scripts
	$('.week-view-sel').buttonset();
	
	$("#week-view-sel-peak").click(function () {
		$('.week-table-avg').fadeOut(100, "swing" , function() {
			$('.week-table-peak').fadeIn(100);
		});
		$('#week-view-sel-avg').next().removeClass('ui-state-active');
	});
	$("#week-view-sel-avg").click(function () {
		$('.week-table-peak').fadeOut(100, "swing" , function() {
			$('.week-table-avg').fadeIn(100);
		});
		$('#week-view-sel-peak').next().removeClass('ui-state-active');
	});
	$("#week-view-sel-peak").click();
	
    var autorefresh = setInterval(function () {
		$("#streamstats").load("./?page=api&task=html_live_stats").fadeIn("slow");
	}, 2500);
	
	$("#search-date").datepicker({dateFormat:'yy-mm-dd', firstDay: 1});
	$("#search-dateto").datepicker({dateFormat:'yy-mm-dd', firstDay: 1});
	
	// Week picker 
	// thanks to http://stackoverflow.com/questions/1289633/how-to-use-jquery-ui-calendar-date-picker-for-week-rather-than-day
	var startDate;
    var endDate;

    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }

    $('.week-picker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $('#startDate').val($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
            $('#endDate').val($.datepicker.formatDate( dateFormat, endDate, inst.settings ));

            selectCurrentWeek();
        },
        beforeShowDay: function(date) {
            var cssClass = '';
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
    });

    $('.week-picker .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.week-picker .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
	
	// Week view top results
	var top = $("#top-1").text();
	$('.table-week tr td').each(function() {
		if($(this).text() == top) {
			$(this).css("font-weight", "bold");	
		}
	});
	
	// User account slider
	$("#user-access-slider").slider({
    	value: 10,
		min: 0,
		max: 40,
		step: 10,
		slide: function( event, ui ) {
			switch(ui.value) {
				case 0:
					$("#user-access-slider-desc").text("0 (Account disabled)");
					break;
				case 10:
					$("#user-access-slider-desc").text("10 (Normal, access to live & search)");
					break;
				case 20:
					$("#user-access-slider-desc").text("20 (Extended, access to live, search & week view)");
					break;
				case 30:
					$("#user-access-slider-desc").text("30 (Stream admin, cccess to all data and stream admin)");
					break;
				case 40:
					$("#user-access-slider-desc").text("40 (RadioPanel admin, access to all parts of site)");
					break;
				case 50:
					$("#user-access-slider-desc").text("50 (How?!)");
					break;
				default:
					$("#user-access-slider-desc").text(ui.value+" (How?!)");
			}
			$("#user-access-slider-input").val(ui.value);
		}
	});
	$("#user-access-slider-desc").text("10 (Normal, access to live & search)");
});
