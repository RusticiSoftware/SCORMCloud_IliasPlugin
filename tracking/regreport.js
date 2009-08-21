// Constants
UNKNOWN = "unknown";

$(document).ready(
        function() {
            // Using straight .getScript rather than the $.getJSON because we
            // can't dynamically generate the function name... it has to be
            // hard-coded so we can generate a valid signature. Furthermore, the
            // jQuery.getScript method cannot be used because it, along with
            // most jQuery calls sends an additional _12323232 timestamp type
            // parameter to make it unique for caching purposes.
            //jQuery.getScript("<%=DataUrl%>");
            $.ajax({
                url: $('#report').attr('dataUrl'), 
                dataType: "script",
                type: "GET", 
                cache: true, 
                callback: null, // The DataUrl includes a json callback method of getRegistrationResultCallback()
                data:null});
        }
    );

function getRegistrationResultCallback(data) {
	
	$.fn.outerHTML = function() {
	    var doc = this[0] ? this[0].ownerDocument : document;
	    return $('<div>', doc).append(this.eq(0).clone()).html();
	};
    
		var regTable = '<table cellspacing=0 cellpadding=0 style="margin-top: 10px; width: 100%" id="regtable"><tr class="tblheader">' +
			'<th style="font-weight: bold" nowrap="nowrap">Activity Title</th>' +
			'<th style="font-weight: bold" nowrap="nowrap">Complete</th>' +
			'<th style="font-weight: bold" nowrap="nowrap">Satisfied</th>' +
			'<th style="font-weight: bold" nowrap="nowrap">Time</th>' +
			'<th style="font-weight: bold" nowrap="nowrap">Score</th>' +
			'<th style="font-weight: bold" nowrap="nowrap"></th>' +
		'</tr></table>';

		$(regTable).appendTo($('#report'));

		renderActivity(data.rsp.registrationreport.activity, $('#report'), true, 0);

		$('.summary_row').click(function() {
			$(this).next().toggle();
			return false;
		}).next().hide();
		
		$(function() {
	        $('.summary_row').hover(function() {
	            $(this).css('background-color', '#E9EFFD');
	        },
	        function() {
	            $(this).css('background-color', 'white');
	        });
	    });
	  
}

// Recursively renders the activity and it's children as nested unordered lists
function renderActivity(activity, parent, isFirst, level) {

 	var hasChildActivities = activity.children && activity.children !== undefined && activity.children !== null && activity.children !== "";

    var satisfied = UNKNOWN;
	var score = "";
	
	try {
		if (activity.objectives.objective[0].progressstatus) {
			var satisfied = activity.objectives.objective[0].satisfiedstatus;
			if (activity.objectives[0].measurestatus) {
				var score = activity.objectives.objective[0].normalizedmeasure * 100;
			} 
		}
	} catch (e2) {
		 try {
			if (activity.objectives.objective.progressstatus) {
				var satisfied = activity.objectives.objective.satisfiedstatus;
				if (activity.objectives.objective.measurestatus) {
					var score = activity.objectives.objective.normalizedmeasure * 100;
				} 
			}
		} catch (e) {
			// leave default uninit
		}
	}
	

    if (activity.attemptprogressstatus){
        var completed = activity.completed;
    }
    else{
        var completed = UNKNOWN;
    }

	if (activity.runtime) {
		var time = activity.runtime.timetracked.substring(2, 10); // allow 99 hours and trim millis
	} else {
		var time = "";
	}

	
	// This is actually a dummy link meant to just give a UI cue since the anywhere on the entire row can be clicked
	// Thi could perhaps be changed to a downward pointing "open me" kind of arrow.
	var detailsLink = "<span style='color: blue; cursor: hand'>details</span>";
	var detailsLink = '<img border="0" style="vertical-align: middle;" src="./templates/default/images/asc_order.gif"/><img border="0" style="margin-left: 1px;vertical-align: middle;" src="./templates/default/images/asc_order.gif"/>';
	

	var activityRow = '<tr class="tblrow1 summary_row">' +
		'<td style="padding-left: ' + level*20 + 'px; border-top: 1px solid black;" class="" nowrap="nowrap">' + activity.title + '</td>' +
		'<td style="border-top: 1px solid black;" class="" nowrap="nowrap">' + "<span style='font-size: 175%' class='" + completed + "'>&bull;</span> " + '</td>' +
		'<td style="border-top: 1px solid black;" class="" nowrap="nowrap">' + "<span style='font-size: 175%' class='" + satisfied + "'>&bull;</span> " + '</td>' +
		'<td style="border-top: 1px solid black;" class="" nowrap="nowrap">' + time + '</td>' +
		'<td style="border-top: 1px solid black;" class="" nowrap="nowrap">' + score + '</td>' +
		'<td style="border-top: 1px solid black;" class="" nowrap="nowrap">' + detailsLink + '</td>' +
	'</tr>';

	$(activityRow).appendTo($('#regtable'));

	// Details, include Activity Data. 
	// attempts, suspended
	// each objective
	// interacdtions
	
	var attempts = 1;
	var suspended = "true";
	var obj = {id: "primary obj", normalizedMmeasure: 0.93, satisfiedStatus: UNKNOWN};
	
	var detailsDiv = $("<div>");
	var objectivesDiv = $("<div style='width: 50%' class='rpt_objectives' id='objectives_" + activity.id + "'><div style='font-size: 90%; font-weight: bold'>Objectives</div></div>");
	var interactionsDiv = $("<div style='width: 50%' class='rpt_interactions' id='interactions_" + activity.id + "'><div style='font-size: 90%; font-weight: bold'>Interactions</div></div>");
	
	objectivesDiv.appendTo(detailsDiv);
	interactionsDiv.appendTo(detailsDiv);
	
	
	if ( activity.runtime && activity.runtime !== undefined && activity.runtime.interactions !== undefined) {
	    var interactionHtml = fmtInteractions(activity.runtime.interactions);
		if (interactionHtml != "") {
			$(fmtInteractions(activity.runtime.interactions)).appendTo(interactionsDiv);
		}
    }

	if(activity.objectives){
        $(fmtObjectives(activity.objectives)).appendTo(objectivesDiv);
    }


	// Put the details into a slot within the table
	var details = $('<tr class="detail_row"><td style="padding-left: ' + level*20 + 'px;" colspan=6>' + detailsDiv.html() + '</td></tr>');

	$(details).appendTo($('#regtable'));

	if (activity.children) {
		$(activity.children.activity).each(function() {
	           renderActivity(this, $('<div>').appendTo(parent).get(), false, level+1);
	       });
	}
}

// Returns the html of one or more lists items representing objective data
function fmtInteractions(interactions) {
    
    if (interactions === undefined || interactions == null || interactions == "") {
        return "";
    }   

    var result = "<ul>";
      
    $(interactions.interaction).each(function(index) {

		result += "<li class='interaction'>";
		result += "<span style='font-size: 150%' class='" + this.result + "'>&bull;</span> " + this.id +
		 ". answered <strong>" + fmtResponse(this.learner_response) + "</strong>";
		if (this.result == "correct") {
			result += ".";
		} else {
			result += ", expected <strong>" + fmtCorrectResponses(this.correct_responses) + "</strong>.";
		}
		result += " <i>" + this.type.toLowerCase() + "</i>";
		result += "</li>";
            
    });

	result += "</ul";

    return result;
}

function fmtObjectives(objectives) {

    if (objectives === undefined) {
        return "";
    }   

    var result = "";

    $(objectives.objective).each(function(index) {
      
        temp_ul = $('<ul style="margin-top: 5px">')
                .append(fmtListItem('Id', this.id))
                .append(fmtListItem('Measure Status', this.measurestatus));
        if(this.measurestatus){
            temp_ul.append(fmtListItem('Normalized Measure', this.normalizedmeasure));
        }
        else{
            temp_ul.append(fmtListItem('Normalized Measure', UNKNOWN));
        }
        temp_ul.append(fmtListItem('Progress Measure', this.progressstatus))
                .append(fmtListItem('Satisfied Status', this.satisfiedstatus));
        result = result + '<li style="margin-top: 5px">' +
            $('<li>')
            .append(index > 0 ? 'Secondary Objective ' + index : "Primary Objective")
            .append(temp_ul)
            .html() +  '</li>';
    });
  
    return result;
}

// Helper to print name/value pairs of activity data
function fmtListItem(name, value) {

    if (value === undefined || value === null) {
        value = "";
    }

    return "<li>" + name + ": <span class='dataValue'>" + value + "</span></li>";
}

function fmtResponse(r) {
	
	r = r.toString();
	r = r.replace(/\[,\]/g, ", ");
	r = r.replace(/\[.\]/g, " -> ");
	
	return r;
}

function fmtCorrectResponses(correctResponses) {
    
    if (correctResponses === undefined || correctResponses == null || correctResponses == "") {
        return "";
    }   

    var result = "";
      
    $(correctResponses.response).each(function(index) {
      
        result = result + 
            $('<div>')
            .append(index + ': ', fmtResponse(this.id))  //TODO: bug in cloud, this shouldn' be id
            .html();
            
    });

    return result;
}



