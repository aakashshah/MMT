function validateSignIn()
{
	var jUserName = document.forms["loginform"]["username"].value;
	if (null == jUserName || "" == jUserName)
	{
		alert("Username cannot be blank!");
		return false;
	}
	
	var jUserName = document.forms["loginform"]["password"].value;
	if (null == jUserName || "" == jUserName)
	{
		alert("Password cannot be blank!");
		return false;
	}
	
	return true;
}
