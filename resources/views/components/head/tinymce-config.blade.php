<script src="https://cdn.tiny.cloud/1/q691pe10pjlsx83ru9f2peuwxlth53vcdkvt4zgc0kcmlprq/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  tinymce.init({
    selector: 'textarea.tinymce-editor',
    height: 400,
    menubar: false,
    plugins: [
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 
      'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    content_style: 'body { font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif; font-size:14px }',
    codesample_languages: [
      {text: 'HTML/XML', value: 'markup'},
      {text: 'JavaScript', value: 'javascript'},
      {text: 'CSS', value: 'css'},
      {text: 'PHP', value: 'php'},
      {text: 'Ruby', value: 'ruby'},
      {text: 'Python', value: 'python'},
      {text: 'Java', value: 'java'},
      {text: 'C', value: 'c'},
      {text: 'C#', value: 'csharp'},
      {text: 'C++', value: 'cpp'}
    ]
  });
</script>
