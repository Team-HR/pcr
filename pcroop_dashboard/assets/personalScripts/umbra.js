function loadingOnClick(){
  e = event.srcElement;
  e.children[0].style.display='none';
  e.children[1].style.display='';
  e.disabled = true;
  console.log(e.children);
}
