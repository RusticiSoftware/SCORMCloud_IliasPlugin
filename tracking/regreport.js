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
    render(data.rsp.registrationreport.activity, $('#report'), true);

		$('.activityTitle').click(function() {
			$(this).next().toggle(100);
			return false;
		});
}

// Recursively renders the activity and it's children as nested unordered lists
function render(activity, parent, isFirst) {

 	var hasChildActivities = activity.children !== undefined && activity.children !== null && activity.children !== "";

	if (hasChildActivities) {
		
		// if (isFirst === null || isFirst === undefined)
		// 	    	$('<span class="activityTitle" >' + activity.title + '</span>').appendTo(parent);
		// 	
		// 	    var ul = $('<ul>');
	    // if(activity.objectives.length > 0 && activity.objectives[0].progressstatus){
	    //     ul.append(fmtListItem('Satisfied', activity.satisfied));
	    // }
	    // else{
	    //     ul.append(fmtListItem('Satisfied', "unknown"));
	    // }
	    // if(activity.attemptprogressstatus){
	    //     ul.append(fmtListItem('Completed', activity.completed));
	    // }
	    // else{
	    //     ul.append(fmtListItem('Completed', "unknown"));
	    // }
	    // ul.append(fmtListItem('Progress Status', activity.progressstatus))
	    //     .append(fmtListItem('Atttempts', activity.attempts))
	    //     .append(fmtListItem('Suspended', activity.suspended))
	    //     .append(fmtListObjectiveItems(activity.objectives))
	    //     .append(fmtRuntime(activity.runtime))
	    //     .appendTo(parent);

		// ul.appendTo(parent);
		
		$(activity.children.activity).each(function() {
            render(this, $('<div>').appendTo(parent).get());
        });
		
	} else {
		
		var div = $('<div class="accordian">');
		var title = $('<a href="#" class="activityTitle" >' + activity.title + '</a>');
		
		if(activity.objectives.length > 0 && activity.objectives[0].progressstatus){
	        var satisfied = activity.satisfied;
	    }
	    else{
	        var satisfied = "unknown";
	    }
	    if(activity.attemptprogressstatus){
	        var completed = activity.completed;
	    }
	    else{
	        var completed = "unknown";
	    }
		
		if (completed == "unknown") {
			var status = "Incomplete";
		} else {
			if (satisfied == "unknown") {
				var status = "Completed";
			} else {
				var status = satisfied;
			}
		}
		
		if (activity.runtime === undefined) {
	        var time = "Not Started";
		} else {
			var time = activity.runtime.total_time;
			
			if (time == "0000:00:00.00") {
				time = activity.runtime.timetracked;
			}
			
			var timeArray = time.split(':');
			var hours = timeArray[0] * 1;
			var minutes = timeArray[1] * 1;
			var seconds = Math.ceil(timeArray[2]) * 1;
			
			if (hours + minutes + seconds ==  0) {
				var timeString = "Learner has not attempted this activity."; 
			} else {
				var timeString = "";
				if (hours > 0) {
					timeString += hours + " hours, ";
				}
				if (minutes > 0 || hours > 0) {
					timeString += minutes +  " minutes and ";
				}
				timeString += seconds + " seconds";
				timeString = "Learner spent " + timeString + " on this activity."
			}
			
		}
		
		var detailsHtml = "Status is <i>" + status + "</i>";
		
		var scoreKnown = false;
		
		if (scoreKnown) {
			detailsHtml += " with a score of " + score;
		}
		detailsHtml += ". "; 
		
		if (time != "Not Started") {
			detailsHtml += timeString;
		} else {
			detailsHtml += "Learner has not spent any time on this activity yet.";
		}
		
		if (activity.runtime !== undefined && activity.runtime.interactions !== undefined) {
	        detailsHtml += '<div class="interactions">' + fmtInteractions(activity.runtime.interactions) + '</div>';
	    }
		
		var details = $('<div class="activity-details">' + detailsHtml + '</div>');
		div.append(title).append(details).append(title).append(details).append(title).append(details).append(title).append(details);
		div.appendTo(parent);
	}
}

// Returns the html of one or more lists items representing objective data
function fmtInteractions(interactions) {
    
    if (interactions === undefined || interactions == null || interactions == "") {
        return "";
    }   

    var result = "";
      
    $(interactions.interaction).each(function(index) {

		result += "<div class='interaction " + this.result + "'>";
		result += this.id + " (" + this.type + "): Answered '" + this.learner_response + "'";
		if (this.result == "correct") {
			result += ".";
		} else {
			result += ", Expected '" + fmtCorrectResponses(this.correct_responses) + "'.";
		}
		result += "</div>";
            
    });

    return result;
}


function fmtCorrectResponses(correctResponses) {
    
    if (correctResponses === undefined || correctResponses == null || correctResponses == "") {
        return "";
    }   

    var result = "";
      
    $(correctResponses.response).each(function(index) {
      
        result = result + 
            $('<div>')
            .append(index + ': ', this.id)
            .html();
            
    });

    return result;
}



