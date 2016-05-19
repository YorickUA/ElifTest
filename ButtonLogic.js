window.onload=function (){
  
    function loadTree() {
        $.post(window.location.href + '?getTree', {data: null}, function (response) {
            response = JSON.parse(response);
            var Tree = "";
            for (var i = 0; i < response.length; i += 1) {
                if (response[i].company_parent == false) {
                    Tree += response[i].company_name + "| " + response[i].company_earnings
                        + (hasSubTree(response[i],response) ? "| "
                        + getSubEarnings(response[i], response) : "") + "<br>";
                    Tree += getSubTree(response[i], response, 1);
                }
            }
            $('#companyTree').html(Tree);

            function getSubEarnings(parentNode, dataTable) {
                var money = 0;
                for (var i = 0; i < dataTable.length; i += 1) {
                    if (parentNode.company_id == dataTable[i].company_parent) {
                        money += getSubEarnings(dataTable[i], dataTable);
                    }
                }
                money += parseInt(parentNode.company_earnings);
                return money;
            }

            function getSubTree(parentNode, dataTable, level) {
                var Tree = "";
                for (var i = 0; i < dataTable.length; i += 1) {
                    if (parentNode.company_id == dataTable[i].company_parent) {
                        for (var j = 0; j < level; j += 1) {
                            Tree += "-";
                        }
                        Tree += dataTable[i].company_name + "| " + dataTable[i].company_earnings
                            + (hasSubTree(dataTable[i], dataTable) ? "| "
                            + getSubEarnings(dataTable[i], dataTable) : "") + "<br>";
                        Tree += getSubTree(dataTable[i], dataTable, level + 1);
                    }
                }
                return Tree;
            }

            function hasSubTree(parentNode, dataTable) {
                for(var i = 0; i < dataTable.length; i += 1) {
                    if (parentNode.company_id == dataTable[i].company_parent) {
                        return true;
                    }
                }
                return false;
            }
       });

    }
     loadTree();

    $('#createCompany').on('click', function() {
        var data={
            name:$('#newCompanyName').val(),
            earnings:$('#newCompanyEarnings').val(),
            parent:$('#newCompanyParent').val()
        };
        $.post(window.location.href + '?createCompany', {data: data}, function(response) {
            if (!parseInt(response)) {
                alert('Error while creating company');
            }
            else loadTree();
        });
    });

    $('#DeleteCompanyButton').on('click', function(){
        $.post(window.location.href+'?deleteCompany',{data:$('#deleteCompany').val()}, function (response){
            if(!parseInt(response)) {
                alert('Error while deleting company');
            }
            else loadTree();
        });
    });

    $('#ShowCompanyButton').on('click', function() {
        $.post(window.location.href+'?showCompany',{data:$('#showCompany').val()}, function (response){
            response=JSON.parse(response);
            var child_comps="";
            for (var i=0; i<  response['child_companies'].length; i+=1) {
                child_comps +=response['child_companies'][i].company_name+" ";
            }
            if(response) {
                $('#ShowCompanyField').html("Company earnings: " + response.company_earnings + ", subsidiary companies: " +  child_comps);

            }
            else{
                alert("No such company");
            }
        });
    });

    $('#EditCompanyButton').on('click', function() {
        var data={
            oldName:$('#editCompanyName').val(),
            name:$('#editedCompanyName').val(),
            earnings:$('#editedCompanyEarnings').val()
        };
        $.post(window.location.href+'?editCompany',{data:data}, function (response) {
            if(!parseInt(response)) {
                alert('Error while editing company');
            }
            else
                loadTree();
        });
    });
};