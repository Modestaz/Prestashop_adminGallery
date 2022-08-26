{* website for 'prestashop' UI *}
{* https://build.prestashop.com/prestashop-ui-kit/ *}

{* PRODUCT PAGE *}
{if $page.page_name == 'product'}

	{if $photos|@count > 0 }
		<h1>{$index_Title}</h1>
		<div class="customerPhotos">

		
			{* TODO: Maybe add some 'Modal', zoom/preview image? *}
			{foreach $photos as $photo }
				<img class="photo" alt="HappyCustomer_{$photo}" src='./var/adminGallery/{$product.id_product}/{$photo}' />
			{/foreach}
		</div>
	{/if}

{* ADMIN PRODUCT PAGE *}
{else}
		
	<h2>{$admin_Title}</h2>
	
	<div class="customerUpload">
		<div class="custom-file">
			<input type="file" class="custom-file-input" id="file">
			<label class="custom-file-label" for="file">Choose files...</label>
		</div>
		
		<button type="button" class="btn btn-primary btn-sm" onclick="uploadFile();">
			<i class="material-icons">vertical_align_bottom</i> UPLOAD
		</button>
	</div>

	{if $photos|@count > 0 }

		<div class="customerPhotos">
		
			{* TODO: Maybe add some 'Modal', zoom/preview image? *}
			{foreach $photos as $photo }
				<img class="photo" alt="HappyCustomer_{$photo}" src='../../../../../var/adminGallery/{$admin_product_id}/{$photo}' />
			{/foreach}
		</div>
	{/if}
	
	<hr>
	
	<!-- by. Modestas -->
	<!-- 1.7.8.7 Prestashop, it is not worth starting to work with back office hooks -->
	<!-- This is the reason why this 'script' is here. -->
	<script>
		
		function uploadFile() {

			var files = document.getElementById("file").files;

			if( files.length > 0 ){

				var formData = new FormData();
				formData.append("file", files[0]);

				var xhttp = new XMLHttpRequest();

				xhttp.open("POST", "../../../../../var/adminGallery/ajaxfile.php?product={$admin_product_id}", true);

				xhttp.onreadystatechange = function() {
					
					if (this.readyState == 4 && this.status == 200) {
						alert( "The file starts uploading to the gallery.\nThe file will be visible after the page reloads!" );

					}
				};

				xhttp.send(formData);

			} else {
				alert( "You must select a file." );
			}
		}

	</script>
	
{/if}