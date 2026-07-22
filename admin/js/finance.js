
//payment transaction
const tabs = document.querySelectorAll('#paymentTabs .nav-link');

const rows = document.querySelectorAll('#paymentTable tr');

tabs.forEach(tab=>{

    tab.addEventListener('click',function(){

        tabs.forEach(t=>t.classList.remove('active'));

        this.classList.add('active');

        let filter=this.dataset.filter;

        rows.forEach(row=>{

            if(filter==="All"){

                row.style.display="";

            }else{

                row.style.display=
                    row.dataset.status===filter
                    ? ""
                    : "none";

            }

        });

    });

});