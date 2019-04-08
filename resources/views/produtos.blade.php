@extends('layout.app', ["current" => "produtos" ])

@section('body')
 <button class="btn btn-primary" role="button" onclick="novoProduto()">Cadastrar Produtos</button>
   
<div class="card border">
    <div class="card-body">
        <h5 class="card-title">Lista de produtos</h5>
        <table id="tabProdutos" class="table table-ordered table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome do Produto</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            	
            </tbody>
           
    
                
        </table>
        
    </div>
    
</div>


   
   
  <div class="modal" tabindex="-1" role="dialog" id="dlgProdutos">
    <div class="modal-dialog" role="document"> 
        <div class="modal-content">
            <form class="form-horizontal" id="formProduto">
                <div class="modal-header">
                    <h5 class="modal-title">Novo produto</h5>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="id" class="form-control">
                    <div class="form-group">
                        <label for="nomeProduto" class="control-label">Nome do Produto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="nomeProduto" placeholder="Nome do produto">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="precoProduto" class="control-label">Preço</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="precoProduto" placeholder="Preço do produto">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantidadeProduto" class="control-label">Quantidade</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="quantidadeProduto" placeholder="Quantidade do produto">
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label for="categoriaProduto" class="control-label">Categoria</label>
                        <div class="input-group">
                            <select class="form-control" id="categoriaProduto" >
                            </select>    
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
	//ajax setup carrega token
	$.ajaxSetup({
		headers:{
			'X-CSRF-TOKEN': '{{csrf_token()}}'
		}
	});

	function novoProduto(){
	$('#dlgProdutos').show();
	
	}

	function listaCategorias(){
		$.get('/api/categorias',function(data){
		$('#categoriaProduto').append('<option value=>Selecione');
		for(i=0;i<data.length;i++){

			opcao = '<option value=' + data[i].id + '> ' + data[i].nome + '</option>';
			$('#categoriaProduto').append(opcao);
		}	
		});
		
	}

	function montarLinha(p){
		var linha = "<tr>" + 
		"<td>" + p.id + 
		"<td>" + p.nome +
		"<td>" + p.preco +
		"<td>" + p.estoque +
		"<td>" + p.categoria_id +
		'<td>' + '<button class="btn btn-primary btn-small" onclick="editar(' +p.id+')" style=margin-right:10px;>Editar </button>' +
		'<button class="btn btn-danger btn-small" onclick="remover(' +p.id+')">Deletar </button>';
		return linha;

	}
 //selecionar o produto para update
    function editar(id){
        $.getJSON('/api/produtos/'+id,function(data){
        $('#id').val(data.id);
        $('#nomeProduto').val(data.nome);
        $('#precoProduto').val(data.preco);
        $('#quantidadeProduto').val(data.estoque);
        $('#categoriaProduto').val( data.categoria_id);  
        $('#dlgProdutos').modal('show');  
        });
       
    }

///deletar o produto
    function remover(id){
        $.ajax({
            type:'DELETE',
            url:"/api/produtos/" + id,
            context:'this',
            success:function(e){
                linha = $('#tabProdutos>tbody>tr');
                e = linha.filter(function(i,elemento){
                    return elemento.cells[0].textContent == id;
                });

                if(e){
                    e.remove();
                }
            },
            error:function(error){
                console.log('Erro!');
            }

        });
    }

	function listaProdutos(){
		$.get('/api/produtos',function(data){
			
			for(i=0;i<data.length;i++){
				linha = montarLinha(data[i]);
				$('#tabProdutos>tbody').append(linha);
			}
		});
	}
///update do produto
    function salvarProduto(){
        prod = { 
            id : $('#id').val(), 
            nome: $("#nomeProduto").val(), 
            preco: $("#precoProduto").val(), 
            estoque: $("#quantidadeProduto").val(), 
            categoria_id: $("#categoriaProduto").val(),
            
        };
       // console.log(prod);
        $.ajax({
            type: "PUT",
            url: "/api/produtos/" + prod.id,
            context: this,
            data: prod,
            success: function(data) {
                prod = JSON.parse(data);
                linha = $('#tabProdutos>tbody>tr');
                e = linha.filter(function(i,e){
                    return e.cells[0].textContent == prod.id;
                });

                if (e) {
                    e[0].cells[0].textContent = prod.id;
                    e[0].cells[1].textContent = prod.nome;
                    e[0].cells[2].textContent = prod.estoque;
                    e[0].cells[3].textContent = prod.preco;
                    e[0].cells[4].textContent = prod.categoria_id;
                }
            },
            erro:function(){
                "erro amigo";
            }
        });
    }
	///function criar produto
	function criarProduto() {
        prod = { 
            nome: $("#nomeProduto").val(), 
            preco: $("#precoProduto").val(), 
            estoque: $("#quantidadeProduto").val(), 
            categoria_id: $("#categoriaProduto").val() 
        };
        $.post("/api/produtos", prod, function(data) {
            //produto = JSON.parse(data);
            linha = montarLinha(data);
            $('#tabelaProdutos>tbody').append(linha); 

        });
    }

	///form produto salvar
	$("#formProduto").submit( function(event){ 
        event.preventDefault();
        if($('#id').val() != ''){ 
       
        salvarProduto();
    }else{ 
          criarProduto();
         location.reload();       
       }
    });

    

	$(function(){
		listaCategorias();
		listaProdutos();
	});
</script>
@endsection