function whoPaidFunction(emailId,divName)
{
          var newdiv = document.createElement('div');
          newdiv.innerHTML = emailId +"&nbsp; &nbsp;  <input type = 'text' name = 'paidAmt[]'  value = 0></input>   <input type='hidden' name = 'paidEmailIds[]' value = " + emailId + "> </input> <br>" ;
          document.getElementById(divName).appendChild(newdiv);
}

function whoParticipatedFunction(emailId,divName)
{
        var newdiv = document.createElement('div');
        newdiv.innerHTML = emailId +"&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \
	Share: <input type = 'text' name = 'sharedAmt[]' value = 0 ></input> <br><input type='hidden' name = 'shareEmailIds[]' value = " + emailId + "> </input> " ;
        document.getElementById(divName).appendChild(newdiv);
}

function whoSettledFunction(emailId,divName)
{
          var newdiv = document.createElement('div');
          newdiv.innerHTML = emailId +"&nbsp; &nbsp;  <input type = 'text' name = 'paidAmt[]'  value = 0></input>   <input type='hidden' name = 'paidEmailIds[]' value = " + emailId + "> </input> <br>" ;
          document.getElementById(divName).appendChild(newdiv);
}


