body {
display: grid;
grid-template-columns: 3fr; /* Auto for d1, 1fr for the rest */
grid-template-rows: 75px auto;
background-color: rgb(16, 88, 91);
}

header {
grid-column: 1/span 3;
grid-row: 1;
display: grid;
grid-template-columns: 25% 50% 25%; /* Auto for d1, 1fr for the rest */
grid-template-rows: 100%;
background-color: rgb(16, 88, 91);
}

.d1 {
grid-column: 2;
}

h1 {
margin: 0;
text-align: center;
color: white;
font-size: 4em;
}

.d2 {
grid-column: 3;
gap: 10px;
padding: 10px;
}

.main {
display: grid;
grid-template-columns: 15% 85%; /* Auto for d1, 1fr for the rest */
grid-template-rows: 20% 80%;
}

nav {
grid-column: 1;
grid-row: 1;
display: flex;
flex-direction: column;


}

section {
width: 100%;
grid-column: 2;
grid-row: 1/span 2;

}

.container {

padding: 1em;
border-style: solid;
border-radius: 20px;
border-width: 3px;
border-color: rgba(82, 120, 122, 0.48);
align-items: center;

background: linear-gradient(180deg, rgba(78, 181, 186, 73) 0%, rgba(42, 241, 250, 98) 100%);
font-size: 1.5em;
margin-top: 5%;
box-sizing: border-box;
margin-left: 5%;
margin-right: 5%;


}

.content_element {
margin-top: 2%;
margin-left: 2%;
margin-right: auto;
background-color: rgb(202, 247, 255);
overflow: hidden;
padding: 15px;
border-radius: 20px;

}

h2 {
color: white;
align-items: center;
text-align: center;

}

.bouton {
border: 0;
line-height: 2.5;
padding: 0 20px;
font-size: 1rem;
text-align: center;
color: #fff;
text-shadow: 1px 1px 1px #000;
border-radius: 10px;
background: linear-gradient(180deg, rgba(78, 181, 186, 73) 0%, rgba(42, 241, 250, 98) 100%);
position: relative;
display: inline-block;
float: right;
}
.boutonNav {

flex: max-content;
max-width: 100%;
border: 0;
line-height: 2.5;
padding: 0 20px;
font-size: 1rem;
text-align: center;
color: #fff;
text-shadow: 1px 1px 1px #000;
border-radius: 10px;
background: linear-gradient(180deg, rgba(78, 181, 186, 73) 0%, rgba(42, 241, 250, 98) 100%);
position: relative;
display: inline-block;
width: 100%;

}

.page {
border: 0;
line-height: 2.5;
padding: 0 20px;
font-size: 1rem;
text-align: center;
color: #fff;
text-shadow: 1px 1px 1px #000;
border-radius: 10px;
background: rgba(78, 181, 186, 73);
}

#pageDiv{
display: flex;

}

#pageContenue{
text-align: center;
}
a {
text-decoration: none;
color: black;
}

.boxPage{
background-color: white;
border-radius: 10px;
}