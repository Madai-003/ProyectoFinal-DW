
(function(){
  const toggle = document.getElementById('navToggle');
const nav = document.getElementById('mainNav');
const backdrop = document.getElementById('navBackdrop');

function openNav(){ nav.classList.add('open'); toggle.classList.add('active'); backdrop.hidden=false; document.body.style.overflow='hidden'; }
function closeNav(){ nav.classList.remove('open'); toggle.classList.remove('active'); backdrop.hidden=true; document.body.style.overflow=''; }

toggle.addEventListener('click', ()=> nav.classList.contains('open') ? closeNav() : openNav());
backdrop.addEventListener('click', closeNav);
nav.addEventListener('click', e => { if(e.target.closest('a')) closeNav(); });
document.addEventListener('keydown', e => { if(e.key==='Escape' && nav.classList.contains('open')) closeNav(); });
})();

