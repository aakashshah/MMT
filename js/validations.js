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

function validateSignUp()
{
	var jVar = document.forms["signupform"]["username"].value;
	if (null == jVar || "" == jVar)
	{
		alert("Username cannot be blank!");
		return false;
	}
	
	var jVar = document.forms["signupform"]["password"].value;
	if (null == jVar || "" == jVar)
	{
		alert("Password cannot be blank!");
		return false;
	}
	
	var jVar2 = document.forms["signupform"]["repassword"].value;
	if (null == jVar2 || "" == jVar2)
	{
		alert("You must re-enter the password!");
		return false;
	}
	
	if (jVar != jVar2)
	{
		alert("Passwords must match!");
		return false;
	}
	
	var jVar = document.forms["signupform"]["name"].value;
	if (null == jVar || "" == jVar)
	{
		alert("Alias cannot be blank!");
		return false;
	}
	
	return true;
}

function validateProfileSettings()
{
	var jVar = document.forms["changeSettings"]["password"].value;
	if (null == jVar || "" == jVar)
	{
		alert("Password cannot be blank!");
		return false;
	}
	
	var jVar2 = document.forms["changeSettings"]["repassword"].value;
	if (null == jVar2 || "" == jVar2)
	{
		alert("You must re-enter the password!");
		return false;
	}
	
	if (jVar != jVar2)
	{
		alert("Passwords must match!");
		return false;
	}
	
	var jVar = document.forms["changeSettings"]["alias"].value;
	if (null == jVar || "" == jVar)
	{
		alert("Alias cannot be blank!");
		return false;
	}
	
	return true;
}
