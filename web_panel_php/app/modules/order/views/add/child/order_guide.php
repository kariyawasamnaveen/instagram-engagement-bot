<style>
  .page-title h1{
    margin-bottom: 5px; }
    .page-title .border-line {
      height: 5px;
      width: 250px;
      background: #4b38b3;
      position: relative;
      border-radius: 30px; }
    .page-title .border-line::before {
      content: '';
      position: absolute;
      left: 0;
      top: -2.7px;
      height: 10px;
      width: 10px;
      border-radius: 50%;
      background: #fa6d7e;
      -webkit-animation-duration: 6s;
      animation-duration: 6s;
      -webkit-animation-timing-function: linear;
      animation-timing-function: linear;
      -webkit-animation-iteration-count: infinite;
      animation-iteration-count: infinite;
      -webkit-animation-name: moveIcon;
      animation-name: moveIcon; }

  @-webkit-keyframes moveIcon {
    from {
      -webkit-transform: translateX(0);
    }
    to { 
      -webkit-transform: translateX(250px);
    }
  }
</style>

<div class="row justify-content-center m-t-50">
  <div class="col-md-10">
    <div class="page-title m-b-30">
      <h1>
        <?php echo get_option('title_attentions_orderpage',"Guides & Descriptions"); ?>
      </h1>
      <div class="border-line"></div>
    </div>
    <div class="content m-t-30">
      <?php echo get_option("guides_and_desc", ""); ?>
    </div>
  </div> 
</div>