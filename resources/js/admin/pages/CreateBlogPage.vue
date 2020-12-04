<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Blogs <Button @click="addModal=true"><Icon type="md-add"/> Create a Blog</Button></p>
					<div class="_input_field">
						<Input v-model="data.title" type="text" placeholder="Title" />
					</div>
					<div class="_overflow _table_div blog_editor">
						 <editor ref="editor" autofocus :config="config" :init-data="initData"  @save="onSave"/>						 
					</div>
					<div class="_input_field">
						<Input  type="textarea" v-model="data.post_excerpt" :rows=4 placeholder="Post Excerpt " />
					</div>
					<div class="_input_field">
						<Select filterable multiple placeholder="Select a Category" v-model="data.category_id">
							<Option v-for="(category,i) in categories" :value="category.id" :key="i">{{ category.categoryName }}</Option>
						</Select>
					</div>
					<div class="_input_field">
						<Input  type="textarea" v-model="data.metaDescription" :rows=4 placeholder="Meta Description" />
					</div>
					
					
					<div class="_input_field">
						<Button @click="save" :loading="isCreating" :dusabled="isCreating">{{isCreating ? 'Please Wait ...' : 'Create Blog'}}</Button>
					</div>

				</div>			
			</div>
		</div>
    </div>
</template>

<script>

	export default {
		data(){
			return{				
				config: {
						image: {
							endpoints: {
								byFile: 'http://localhost:8090/image',
								byUrl: 'http://localhost:8090/image-by-url',
							},
							field: 'image',
							types: 'image/*',
							},
					},
			
				initData: null,
				data:{
					title : '',
					post : '',
					post_excerpt : '',
					metaDescription : '',
					category_id: [],
					jsonData: null,
				},
				articleHTML: '',
				categories: [],			
				isCreating: false,
				
			}
		},

		methods:{
			upload(){
				console.log('Nice')
			},
			async onSave(response){
				var data = response;
				await this.outputHtml(data.blocks);
				this.data.post = this.articleHTML
				this.data.jsonData = JSON.stringify(data)

				this.isCreating = true;
				const res = await this.callApi('post','app/create_blog', this.data);
				if (res.status == 201) {
					this.s('Blog has been created successfully!!');
				} else {
					this.swr();
				}
				this.isCreating = false;
			},
			async save(){
				this.$refs.editor.save()
			},
			outputHtml(articleObj){
				articleObj.map(obj => {
				switch (obj.type) {
					case 'paragraph':
						this.articleHTML += this.makeParagraph(obj);
						break;
					case 'image':
						this.articleHTML += this.makeImage(obj);
						break;
					case 'header':
						this.articleHTML += this.makeHeader(obj);
						break;
					case 'raw':
						this.articleHTML += `<div class="ce-block">
						<div class="ce-block__content">
							<div class="ce-code">
								<code>${obj.data.html}</code>
							</div>
							</div>
						</div>\n`;
						break;
					case 'code':
						this.articleHTML += this.makeCode(obj);
						break;
					case 'list':
						this.articleHTML += this.makeList(obj)
						break;
					case "quote":
						this.articleHTML += this.makeQuote(obj)
						break;
					case "warning":
						this.articleHTML += this.makeWarning(obj)
						break;
					case "checklist":
						this.articleHTML += this.makeChecklist(obj)
						break;
					case "embed":
						this.articleHTML += this.makeEmbed(obj)
						break;
					case 'delimeter':
						this.articleHTML += this.makeDelimeter(obj);
						break;
					default:
						return '';
					}
				});
			},

		},
		async created(){
			const res = await this.callApi('get','app/get_categories');
			if (res.status == 200) {
				this.categories = res.data
			} else {
				this.swr();				
			}

		}
	

	}
</script>



<style>
	.blog_editor {
		width: 717px;
		margin-left: 160px;
		padding: 4px 7px;
		font-size: 14px;
		border: 1px solid #dcdee2;
		border-radius: 4px;
		color: #515a6e;
		background-color: #fff;
		background-image: none;
		z-index:  -1;
	}
	.blog_editor:hover {
		border: 1px solid #57a3f3;
	}
	._input_field{
		margin: 20px 0 20px 160px;
    	width: 717px;
	}
</style>