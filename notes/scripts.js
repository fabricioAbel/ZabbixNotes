function show(divId, value)
{
  if(value == "S")
    document.getElementById(divId).style.display = "";  
  else
    document.getElementById(divId).style.display = "none";
}

function salvarInfo(form)
{
  //if(form.textInfo.value.trim().length == 0)
  //{
     //alert('Informe corretamente as informações compĺementares!');
     //form.textInfo.focus();
     //return false;
  //}
  form.acao.value = 5;
  form.submit();

}
function salvar(form)
{


  if(form.txtHost.value.trim().length == 0)
  {
    alert('Informe corretamente o Host!');
    // ESTÁ COMENTADO POIS FICA RECARREGANDO A TODO INSTANTE
    //form.txtHost.focus(); 
    return false;
  }
  
  if(form.rdExpediente[1].checked && form.textExpediente.value.trim().length == 0)
  {
    alert('Informe corretamente a ação durante Expediente!');
    form.textExpediente.focus();
    return false;
  }
  if(form.rdExtra[1].checked && form.textExtra.value.trim().length == 0)
  {
    alert('Informe corretamente a ação Extra Expediente!');
    form.textExtra.focus();
    return false;
  }

  form.acao.value = 1;
  form.submit();
}

function remover(form, host)
{  
  if(host.trim() == "")
  {
    alert('Informe primeiramente o host que deseja remover o Notes!');
    form.txtHost.focus();
    return false;
  } 

   if(confirm("Deseja realmente excluir o Notes do host "+host+" ?"))
   {
	form.acao.value=2;
	form.submit();
   }
}

function carregaInfo(form)
{
   form.acao.value=3;
   form.submit();
}
function logout(form)
{
  form.acao.value =6;
  form.submit();
}
function clonar(form, host, host_modelo)
{
  if(host.trim() == "")
  {
    alert('Informe primeiramente o host que deseja receber as informações da clonagem!');
    form.txtHost.focus();
    return false;
  } 
  if(confirm("Confirma a clonagem do Notes do host "+host_modelo+ " para o host "+host+" ?"))
  {
  	form.acao.value=4;
	form.submit();
  }
}

function addUser(form)
{
   
   if(form.txtUser.value.trim().length == 0)
   {
	alert('Informe corretamente o usuário a ser adicionado.');
        form.txtUser.focus();
	return false;
   }
   /*if(autentica == 'P' && form.pwdSenha.value.trim().length < 6)
    {
	alert('Informe corretamente a senha do usuário. Mínimo de 6 dígitos.');
        form.pwdSenha.focus();
	return false;
   }
   */
   form.acao.value = 7;
   form.submit();
}

function removeUser(form)
{
   if(confirm("Deseja realmente retirar o acesso do usuário selecionado?"))
   {
     form.acao.value = 8;
     form.submit();
   }
}
function updateNotes(form, trecho1, trecho2)
{
  if(confirm("Deseja realmente substituir todas as ocorrências do trecho '"+trecho1+"' por: '"+trecho2+"' ?"))
  {
    form.acao.value = 9;
    form.submit();
  }
}
 
