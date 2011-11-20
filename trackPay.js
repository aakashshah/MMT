function whoPaidFunction(emailId)
{
	document.getElementById("whoPaid").innerHTML = document.getElementById("whoPaid").innerHTML + emailId + "<input type = 'text' name = 'paidAmt[]' value = 0 ></input>   <input type='hidden' name = 'paidEmailIds[]' value = " + emailId + "> </input> <br>" ;
}

function whoParticipatedFunction(emailId)
{
	document.getElementById("whoParticipated").innerHTML = document.getElementById("whoParticipated").innerHTML + emailId + "&nbsp;\
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
	Share: <input type = 'text' name = 'participatedAmt[]' value = 0 ></input> <br><input type='hidden' name = 'shareEmailIds[]' value = " + emailId + "> </input> " ;
}
