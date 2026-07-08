<?php
$niche_categories = [
    'sports' => ['name' => '⚽ Sports', 'desc' => 'Team games, solo sports, martial arts, extreme sports, racing, etc.'],
    'media' => ['name' => '📰 Media', 'desc' => 'News, social media, books, magazines, TV, videos, streaming, etc.'],
    'cosmetology' => ['name' => '💄 Cosmetology', 'desc' => 'Hair styling, makeup, skin care, nail art, beauty products, etc.'],
    'travel' => ['name' => '✈️ Travel', 'desc' => 'Cool places, hotels, planes, trains, packing gear, fun trips, etc.'],
    'art' => ['name' => '🎨 Art', 'desc' => 'Drawing, painting, digital art, acting, dance, crafts, famous artists, etc.'],
    'food' => ['name' => '🍕 Food', 'desc' => 'Global foods, ingredients, restaurants, food history, drinks, etc.'],
    'fitness' => ['name' => '🏋️ Fitness', 'desc' => 'Workouts, yoga, stretching, gym clothes, exercise gear, healthy eating, etc.'],
    'cinema' => ['name' => '🎬 Cinema', 'desc' => 'Movie types, how movies are made, awards, reviews, old classics, etc.'],
    'music' => ['name' => '🎵 Music', 'desc' => 'Song styles, instruments, famous singers, writing songs, headphones, speakers, etc.'],
    'automotive' => ['name' => '🚗 Automotive', 'desc' => 'Cars, trucks, fixing cars, cool parts, car shows, test drives, etc.'],
    'fashion' => ['name' => '👗 Fashion', 'desc' => 'Clothes, shoes, cool accessories, designer styles, what\'s trending, etc.'],
    'services' => ['name' => '🛠️ Services', 'desc' => 'Helpers, fixing houses, haircuts, computer help, delivery, etc.'],
    'business' => ['name' => '💼 Business', 'desc' => 'Starting a company, money, investing, ads, being a boss, shopping trends, etc.'],
    'agriculture' => ['name' => '🚜 Agriculture', 'desc' => 'Growing food, farm animals, tractors, green farming, selling crops, etc.'],
    'animals' => ['name' => '🦁 Animals', 'desc' => 'Pets, wild animals, sea life, bugs, animal doctors, etc.'],
    'cooking' => ['name' => '🧑‍🍳 Cooking', 'desc' => 'Recipes, kitchen skills, pots, pans, meal prep, baking treats, etc.'],
    'comedy' => ['name' => '😂 Comedy', 'desc' => 'Stand-up jokes, funny skits, funny TV shows, pranks, silly humor, etc.'],
    'professional' => ['name' => '👔 Professional', 'desc' => 'Business suits, fancy offices, important meetings, briefcases, big company jobs, etc.'],
    'videography' => ['name' => '📹 Videography', 'desc' => 'Cameras, video lights, microphones, video editing, making YouTube videos, etc.'],
    'photography' => ['name' => '📷 Photography', 'desc' => 'Taking pictures, camera gear, settings, editing photos, framing a shot, etc.'],
    'memes' => ['name' => '👾 Memes', 'desc' => 'Funny internet pictures, viral jokes, silly edits, inside jokes, trending sounds, etc.'],
    'trends' => ['name' => '🔥 Trends', 'desc' => 'What\'s viral, cool new gadgets, popular styles, internet challenges, fads, etc.'],
    'outdoors' => ['name' => '🏕️ Outdoors', 'desc' => 'Hiking, camping, survival, water sports, looking at nature, national parks, etc.'],
    'gaming' => ['name' => '🎮 Gaming', 'desc' => 'Video games, mobile games, pro gaming, making games, board games, etc.'],
];
?>

<!-- DYNAMIC VIP SERVICES CONTAINER -->
<div class="dynamic-vip-forms-container">

  <!-- ==============================================
       SERVICE 1: VIP LIKES ENGINE
       ============================================== -->
  <div id="form-service-1" class="dynamic-form-section premium-vip-card glass-morphism mb-4 p-4" style="border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 20px; background: linear-gradient(145deg, rgba(18,18,18,0.9), rgba(0,0,0,0.95)); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
      <div class="row align-items-center mb-4">
        <div class="col-auto">
          <div class="premium-icon-box" style="background: rgba(212, 175, 55, 0.2); border: 2px solid var(--premium-gold); box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fe fe-heart" style="color: var(--premium-gold);"></i>
          </div>
        </div>
        <div class="col">
          <h4 class="mb-0 text-white" style="font-weight: 800; letter-spacing: 1px; font-family: 'Anubis Mythical', sans-serif;">VIP LIKES <span style="color: var(--premium-gold);">ENGINE</span></h4>
          <p class="text-white-50 small mb-0">Algorithmic Post Boosting</p>
        </div>
      </div>

      <!-- Validated Post URL -->
      <div class="form-group mb-4">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target Instagram Post Link</label>
        <input class="form-control premium-input" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 50px;" name="link" type="url" placeholder="https://www.instagram.com/p/...">
        <small class="form-text text-warning mt-2" style="font-size: 10px; font-weight: 600;"><i class="fe fe-shield"></i> System validates URL format to prevent execution errors.</small>
      </div>

      <!-- Safe Velocity Mix -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Delivery Velocity (Safety Regulated)</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="likes_speed_organic" name="speed" value="organic" checked>
        <label for="likes_speed_organic"> organic natural Drip (Safe)</label>
        <input type="radio" id="likes_speed_instant" name="speed" value="instant">
        <label for="likes_speed_instant"> accelerated</label>
      </div>

      <!-- Demographic Targeting -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target AI Fleet Demographic</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="likes_gender_mixed" name="gender_filter" value="all" checked>
        <label for="likes_gender_mixed"> Mixed</label>
        <input type="radio" id="likes_gender_male" name="gender_filter" value="male">
        <label for="likes_gender_male"> Male</label>
        <input type="radio" id="likes_gender_female" name="gender_filter" value="female">
        <label for="likes_gender_female"> Female</label>
      </div>

      <!-- Hidden Input for Database tag_filter -->
      <input type="hidden" class="niche-final-tag-filter" name="tag_filter" value="all">

      <!-- Dynamic Targeted Niche Category Selection -->
      <div class="form-group mb-4 niche-category-selector-container">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;"><i class="fe fe-tag mr-2"></i>Target Campaign Niche Category</label>
        
        <div class="custom-select-container-niche" style="position: relative;">
          <div class="custom-select-trigger-niche" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 50px; display: flex; align-items: center; padding: 0 15px; font-size: 14px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
            <span class="selected-text-niche"> All (Mixed Niche)</span>
            <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 16px; transition: transform 0.3s ease;"></i>
          </div>

          <div class="custom-select-options-niche" style="position: absolute; top: 100%; left: 0; width: 100%; max-height: 230px; overflow-y: auto; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 12px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); backdrop-filter: blur(20px);">
            <div class="custom-option-niche" data-value="all" data-desc="Delivers natural engagement from random general accounts.">
              <span> All (Mixed Niche)</span>
            </div>
            <?php foreach ($niche_categories as $key => $niche) : ?>
              <div class="custom-option-niche" data-value="<?= esc($key) ?>" data-desc="<?= esc($niche['desc']) ?>">
                <span><?= esc($niche['name']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="custom-option-niche" data-value="other" data-desc="Type your custom targeted niche category below.">
              <span> Other / Suggest Category</span>
            </div>
          </div>
        </div>
        
        <div class="niche-desc-badge mt-2" style="font-size: 10px; color: rgba(255,255,255,0.4); display: flex; align-items: center; background: rgba(212,175,55,0.05); border: 1px dashed rgba(212,175,55,0.2); padding: 8px 12px; border-radius: 8px;">
          <i class="fe fe-info mr-2" style="color: var(--premium-gold); font-size: 12px;"></i>
          <span class="niche-desc-text">Delivers natural engagement from random general accounts.</span>
        </div>
      </div>

      <!-- Custom Suggestion Input -->
      <div class="form-group mb-4 custom-niche-input-container" style="display: none;">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 0.5px; font-size: 10px; text-transform: none;"><i class="fe fe-edit-3"></i> Type Custom Category Suggestion</label>
        <input class="form-control premium-input custom-niche-suggest-field" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 45px;" type="text" placeholder="e.g. Real Estate, Crypto, etc.">
      </div>
  </div>


  <!-- ==============================================
       SERVICE 2: VIP FOLLOWS ENGINE
       ============================================== -->
  <div id="form-service-2" class="dynamic-form-section premium-vip-card glass-morphism mb-4 p-4" style="border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 20px; background: linear-gradient(145deg, rgba(18,18,18,0.9), rgba(0,0,0,0.95)); box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: none;">
      <div class="row align-items-center mb-4">
        <div class="col-auto">
          <div class="premium-icon-box" style="background: rgba(212, 175, 55, 0.2); border: 2px solid var(--premium-gold); box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fe fe-users" style="color: var(--premium-gold);"></i>
          </div>
        </div>
        <div class="col">
          <h4 class="mb-0 text-white" style="font-weight: 800; letter-spacing: 1px; font-family: 'Anubis Mythical', sans-serif;">VIP FOLLOWS <span style="color: var(--premium-gold);">ENGINE</span></h4>
          <p class="text-white-50 small mb-0">High-Retention Audience Injection</p>
        </div>
      </div>

      <!-- Validated Username -->
      <div class="form-group mb-4">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target Instagram Username</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent border-right-0" style="border-color: rgba(212,175,55,0.3); color: var(--premium-gold); border-radius: 12px 0 0 12px; font-weight: 700;">@</span>
          </div>
          <input class="form-control premium-input border-left-0" style="padding-left: 0; box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 0 12px 12px 0; height: 50px;" name="follows_username" type="text" placeholder="e.g. cr7_official">
        </div>
        <small class="text-warning mt-1 d-block"><i class="fe fe-check-circle"></i> Must be a public account. Randomized AI delays applied.</small>
      </div>

      <!-- Safe Growth Speed -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Growth Speed (Action-Block Protected)</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="follows_speed_organic" name="follows_speed" value="organic" checked>
        <label for="follows_speed_organic"> Organic Human (Safe)</label>
        <input type="radio" id="follows_speed_steady" name="follows_speed" value="steady">
        <label for="follows_speed_steady"> Steady Scale</label>
      </div>

      <!-- Demographic Targeting (For Follows) -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target AI Fleet Demographic</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="follows_gender_mixed" name="follows_gender_filter" value="all" checked>
        <label for="follows_gender_mixed"> Mixed</label>
        <input type="radio" id="follows_gender_male" name="follows_gender_filter" value="male">
        <label for="follows_gender_male"> Male</label>
        <input type="radio" id="follows_gender_female" name="follows_gender_filter" value="female">
        <label for="follows_gender_female"> Female</label>
      </div>

      <!-- Hidden Input for Database tag_filter -->
      <input type="hidden" class="niche-final-tag-filter" name="tag_filter" value="all">

      <!-- Dynamic Targeted Niche Category Selection -->
      <div class="form-group mb-4 niche-category-selector-container">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;"><i class="fe fe-tag mr-2"></i>Target Campaign Niche Category</label>
        
        <div class="custom-select-container-niche" style="position: relative;">
          <div class="custom-select-trigger-niche" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 50px; display: flex; align-items: center; padding: 0 15px; font-size: 14px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
            <span class="selected-text-niche"> All (Mixed Niche)</span>
            <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 16px; transition: transform 0.3s ease;"></i>
          </div>

          <div class="custom-select-options-niche" style="position: absolute; top: 100%; left: 0; width: 100%; max-height: 230px; overflow-y: auto; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 12px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); backdrop-filter: blur(20px);">
            <div class="custom-option-niche" data-value="all" data-desc="Delivers natural engagement from random general accounts.">
              <span> All (Mixed Niche)</span>
            </div>
            <?php foreach ($niche_categories as $key => $niche) : ?>
              <div class="custom-option-niche" data-value="<?= esc($key) ?>" data-desc="<?= esc($niche['desc']) ?>">
                <span><?= esc($niche['name']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="custom-option-niche" data-value="other" data-desc="Type your custom targeted niche category below.">
              <span> Other / Suggest Category</span>
            </div>
          </div>
        </div>
        
        <div class="niche-desc-badge mt-2" style="font-size: 10px; color: rgba(255,255,255,0.4); display: flex; align-items: center; background: rgba(212,175,55,0.05); border: 1px dashed rgba(212,175,55,0.2); padding: 8px 12px; border-radius: 8px;">
          <i class="fe fe-info mr-2" style="color: var(--premium-gold); font-size: 12px;"></i>
          <span class="niche-desc-text">Delivers natural engagement from random general accounts.</span>
        </div>
      </div>

      <!-- Custom Suggestion Input -->
      <div class="form-group mb-4 custom-niche-input-container" style="display: none;">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 0.5px; font-size: 10px; text-transform: none;"><i class="fe fe-edit-3"></i> Type Custom Category Suggestion</label>
        <input class="form-control premium-input custom-niche-suggest-field" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 45px;" type="text" placeholder="e.g. Real Estate, Crypto, etc.">
      </div>
  </div>


  <!-- ==============================================
       SERVICE 3: VIP COMMENTS ENGINE
       ============================================== -->
  <div id="form-service-3" class="dynamic-form-section premium-vip-card glass-morphism mb-4 p-4" style="border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 20px; background: linear-gradient(145deg, rgba(18,18,18,0.9), rgba(0,0,0,0.95)); box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: none;">
      <div class="row align-items-center mb-4">
        <div class="col-auto">
          <div class="premium-icon-box" style="background: rgba(212, 175, 55, 0.2); border: 2px solid var(--premium-gold); box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fe fe-message-square" style="color: var(--premium-gold);"></i>
          </div>
        </div>
        <div class="col">
          <h4 class="mb-0 text-white" style="font-weight: 800; letter-spacing: 1px; font-family: 'Anubis Mythical', sans-serif;">VIP COMMENTS <span style="color: var(--premium-gold);">ENGINE</span></h4>
          <p class="text-white-50 small mb-0">Niche Relevant Engagement</p>
        </div>
      </div>

      <!-- Validated Post URL -->
      <div class="form-group mb-4">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target Instagram Post Link</label>
        <input class="form-control premium-input" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 50px;" name="comments_url" type="url" placeholder="https://www.instagram.com/p/...">
      </div>

      <!-- Comment Strategy -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Comment Strategy (Anti-Spam)</label>
      <div class="premium-segmented-control mb-3">
        <input type="radio" id="comments_strat_ai" name="comments_strategy" value="ai" checked>
        <label for="comments_strat_ai"> AI Generated (Safe)</label>
        <input type="radio" id="comments_strat_custom" name="comments_strategy" value="custom">
        <label for="comments_strat_custom"> Custom List</label>
      </div>

      <!-- Custom Comments Box -->
      <div class="form-group mb-4 custom-comments-box" style="display: none;">
        <label class="premium-label text-warning" style="font-weight: 600; font-size: 10px; text-transform: none;"><i class="fe fe-alert-triangle"></i> Enter at least 5 unique comments (one per line) to prevent IG spam bans.</label>
        <textarea class="form-control premium-input" name="custom_comments" rows="4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.3); color: white; border-radius: 12px;" placeholder="Great post!&#10;Love this vibe 🔥&#10;So inspiring!&#10;Keep it up 🙌&#10;Amazing shot!"></textarea>
      </div>

      <!-- Demographic Targeting (For Comments) -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target AI Fleet Demographic</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="comments_gender_mixed" name="comments_gender_filter" value="all" checked>
        <label for="comments_gender_mixed"> Mixed</label>
        <input type="radio" id="comments_gender_male" name="comments_gender_filter" value="male">
        <label for="comments_gender_male"> Male</label>
        <input type="radio" id="comments_gender_female" name="comments_gender_filter" value="female">
        <label for="comments_gender_female"> Female</label>
      </div>

      <!-- Hidden Input for Database tag_filter -->
      <input type="hidden" class="niche-final-tag-filter" name="tag_filter" value="all">

      <!-- Dynamic Targeted Niche Category Selection -->
      <div class="form-group mb-4 niche-category-selector-container">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;"><i class="fe fe-tag mr-2"></i>Target Campaign Niche Category</label>
        
        <div class="custom-select-container-niche" style="position: relative;">
          <div class="custom-select-trigger-niche" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 50px; display: flex; align-items: center; padding: 0 15px; font-size: 14px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
            <span class="selected-text-niche"> All (Mixed Niche)</span>
            <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 16px; transition: transform 0.3s ease;"></i>
          </div>

          <div class="custom-select-options-niche" style="position: absolute; top: 100%; left: 0; width: 100%; max-height: 230px; overflow-y: auto; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 12px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); backdrop-filter: blur(20px);">
            <div class="custom-option-niche" data-value="all" data-desc="Delivers natural engagement from random general accounts.">
              <span> All (Mixed Niche)</span>
            </div>
            <?php foreach ($niche_categories as $key => $niche) : ?>
              <div class="custom-option-niche" data-value="<?= esc($key) ?>" data-desc="<?= esc($niche['desc']) ?>">
                <span><?= esc($niche['name']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="custom-option-niche" data-value="other" data-desc="Type your custom targeted niche category below.">
              <span> Other / Suggest Category</span>
            </div>
          </div>
        </div>
        
        <div class="niche-desc-badge mt-2" style="font-size: 10px; color: rgba(255,255,255,0.4); display: flex; align-items: center; background: rgba(212,175,55,0.05); border: 1px dashed rgba(212,175,55,0.2); padding: 8px 12px; border-radius: 8px;">
          <i class="fe fe-info mr-2" style="color: var(--premium-gold); font-size: 12px;"></i>
          <span class="niche-desc-text">Delivers natural engagement from random general accounts.</span>
        </div>
      </div>

      <!-- Custom Suggestion Input -->
      <div class="form-group mb-4 custom-niche-input-container" style="display: none;">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 0.5px; font-size: 10px; text-transform: none;"><i class="fe fe-edit-3"></i> Type Custom Category Suggestion</label>
        <input class="form-control premium-input custom-niche-suggest-field" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 45px;" type="text" placeholder="e.g. Real Estate, Crypto, etc.">
      </div>
  </div>


  <!-- ==============================================
       SERVICE 4: VIRAL MULTIPLIER
       ============================================== -->
  <div id="form-service-4" class="dynamic-form-section premium-vip-card glass-morphism mb-4 p-4" style="border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 20px; background: linear-gradient(145deg, rgba(18,18,18,0.9), rgba(0,0,0,0.95)); box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: none;">
      <div class="row align-items-center mb-4">
        <div class="col-auto">
          <div class="premium-icon-box" style="background: rgba(212, 175, 55, 0.2); border: 2px solid var(--premium-gold); box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fe fe-share-2" style="color: var(--premium-gold);"></i>
          </div>
        </div>
        <div class="col">
          <h4 class="mb-0 text-white" style="font-weight: 800; letter-spacing: 1px; font-family: 'Anubis Mythical', sans-serif;">VIRAL <span style="color: var(--premium-gold);">MULTIPLIER</span></h4>
          <p class="text-white-50 small mb-0">Algorithmic Explore Page Push</p>
        </div>
      </div>

      <!-- Validated Post URL -->
      <div class="form-group mb-4">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target Viral Post Link</label>
        <input class="form-control premium-input" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 50px;" name="viral_url" type="url" placeholder="https://www.instagram.com/p/...">
      </div>

      <!-- Safe Action Mix -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Algorithmic Action Mix</label>
      
      <div class="custom-control custom-checkbox mb-2">
        <input type="checkbox" class="custom-control-input" id="viral_saves" name="viral_saves" checked>
        <label class="custom-control-label text-white-50" for="viral_saves" style="cursor: pointer;"> Algorithm Saves (Safest, High Impact)</label>
      </div>
      
      <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="viral_reposts" name="viral_reposts" checked>
        <label class="custom-control-label text-white-50" for="viral_reposts" style="cursor: pointer;"> Story Reposts (Safe Traction)</label>
      </div>

      <!-- Delivery Strategy -->
      <label class="premium-label mt-2" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Push Strategy</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="viral_speed_24" name="viral_speed" value="24h" checked>
        <label for="viral_speed_24"> 24h Organic Spread</label>
        <input type="radio" id="viral_speed_12" name="viral_speed" value="12h">
        <label for="viral_speed_12"> 12h Momentum</label>
      </div>

      <!-- Demographic Targeting (For Viral) -->
      <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;">Target AI Fleet Demographic</label>
      <div class="premium-segmented-control mb-4">
        <input type="radio" id="viral_gender_mixed" name="viral_gender_filter" value="all" checked>
        <label for="viral_gender_mixed"> Mixed</label>
        <input type="radio" id="viral_gender_male" name="viral_gender_filter" value="male">
        <label for="viral_gender_male"> Male</label>
        <input type="radio" id="viral_gender_female" name="viral_gender_filter" value="female">
        <label for="viral_gender_female"> Female</label>
      </div>

      <!-- Hidden Input for Database tag_filter -->
      <input type="hidden" class="niche-final-tag-filter" name="tag_filter" value="all">

      <!-- Dynamic Targeted Niche Category Selection -->
      <div class="form-group mb-4 niche-category-selector-container">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 11px;"><i class="fe fe-tag mr-2"></i>Target Campaign Niche Category</label>
        
        <div class="custom-select-container-niche" style="position: relative;">
          <div class="custom-select-trigger-niche" style="background: rgba(11,11,12,0.9); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 12px; height: 50px; display: flex; align-items: center; padding: 0 15px; font-size: 14px; font-weight: 600; box-shadow: 0 5px 15px rgba(0,0,0,0.3); cursor: pointer; justify-content: space-between; transition: all 0.3s ease;">
            <span class="selected-text-niche"> All (Mixed Niche)</span>
            <i class="fe fe-chevron-down" style="color: var(--premium-gold); font-size: 16px; transition: transform 0.3s ease;"></i>
          </div>

          <div class="custom-select-options-niche" style="position: absolute; top: 100%; left: 0; width: 100%; max-height: 230px; overflow-y: auto; background: linear-gradient(145deg, rgba(20,20,20,0.98), rgba(5,5,5,1)); border: 1px solid rgba(212,175,55,0.3); border-radius: 12px; margin-top: 8px; z-index: 999; display: none; box-shadow: 0 10px 40px rgba(0,0,0,0.9); backdrop-filter: blur(20px);">
            <div class="custom-option-niche" data-value="all" data-desc="Delivers natural engagement from random general accounts.">
              <span> All (Mixed Niche)</span>
            </div>
            <?php foreach ($niche_categories as $key => $niche) : ?>
              <div class="custom-option-niche" data-value="<?= esc($key) ?>" data-desc="<?= esc($niche['desc']) ?>">
                <span><?= esc($niche['name']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="custom-option-niche" data-value="other" data-desc="Type your custom targeted niche category below.">
              <span> Other / Suggest Category</span>
            </div>
          </div>
        </div>
        
        <div class="niche-desc-badge mt-2" style="font-size: 10px; color: rgba(255,255,255,0.4); display: flex; align-items: center; background: rgba(212, 175, 55, 0.05); border: 1px dashed rgba(212, 175, 55, 0.2); padding: 8px 12px; border-radius: 8px;">
          <i class="fe fe-info mr-2" style="color: var(--premium-gold); font-size: 12px;"></i>
          <span class="niche-desc-text">Delivers natural engagement from random general accounts.</span>
        </div>
      </div>

      <!-- Custom Suggestion Input -->
      <div class="form-group mb-4 custom-niche-input-container" style="display: none;">
        <label class="premium-label" style="color: var(--premium-gold); font-weight: 700; letter-spacing: 0.5px; font-size: 10px; text-transform: none;"><i class="fe fe-edit-3"></i> Type Custom Category Suggestion</label>
        <input class="form-control premium-input custom-niche-suggest-field" style="box-shadow: none; border-color: rgba(212,175,55,0.3); color: white; background: rgba(255,255,255,0.05); border-radius: 12px; height: 45px;" type="text" placeholder="e.g. Real Estate, Crypto, etc.">
      </div>
  </div>

</div>
<!-- END DYNAMIC VIP SERVICES CONTAINER -->

<script>
  $(document).ready(function() {
      // Toggle custom comments box based on strategy
      $('input[name="comments_strategy"]').on('change', function() {
          if($(this).val() === 'custom') {
              $('.custom-comments-box').slideDown(200);
          } else {
              $('.custom-comments-box').slideUp(200);
          }
      });

      // Custom Select UI Logic for Niche Categories
      $(document).on('click', '.custom-select-trigger-niche', function(e) {
          e.stopPropagation();
          $('.custom-select-container').removeClass('open');
          $('.custom-select-options').fadeOut(200);
          $('.custom-select-container-platform').removeClass('open');
          $('.custom-select-options-platform').fadeOut(200);
          
          var container = $(this).parent('.custom-select-container-niche');
          $('.custom-select-container-niche').not(container).removeClass('open');
          $('.custom-select-options-niche').not(container.find('.custom-select-options-niche')).fadeOut(200);
          
          container.toggleClass('open');
          $(this).siblings('.custom-select-options-niche').fadeToggle(200);
      });

      $(document).on('click', '.custom-option-niche', function(e) {
          e.stopPropagation();
          var value = $(this).data('value');
          var text = $(this).text();
          var desc = $(this).data('desc');
          
          var container = $(this).closest('.custom-select-container-niche');
          container.find('.selected-text-niche').text(text);
          container.removeClass('open');
          $(this).closest('.custom-select-options-niche').fadeOut(200);
          
          var section = $(this).closest('.dynamic-form-section');
          var suggestContainer = section.find('.custom-niche-input-container');
          
          if (value === 'other') {
              suggestContainer.slideDown(200);
              var customVal = suggestContainer.find('.custom-niche-suggest-field').val().trim().toLowerCase();
              section.find('.niche-final-tag-filter').val(customVal ? customVal : 'all');
          } else {
              suggestContainer.slideUp(200);
              if (value === 'all') {
                  // Fall back to gender filter if mixed
                  var genderInputName = section.attr('id') === 'form-service-1' ? 'gender_filter' : 
                                      (section.attr('id') === 'form-service-2' ? 'follows_gender_filter' : 
                                      (section.attr('id') === 'form-service-3' ? 'comments_gender_filter' : 'viral_gender_filter'));
                  var genderVal = section.find('input[name="' + genderInputName + '"]:checked').val() || 'all';
                  section.find('.niche-final-tag-filter').val(genderVal);
              } else {
                  section.find('.niche-final-tag-filter').val(value);
              }
          }
          
          // Update description badge
          section.find('.niche-desc-text').text(desc);
      });

      // Handle custom category typing
      $(document).on('input change', '.custom-niche-suggest-field', function() {
          var section = $(this).closest('.dynamic-form-section');
          var val = $(this).val().trim().toLowerCase();
          section.find('.niche-final-tag-filter').val(val ? val : 'all');
      });

      // Handle gender radio button change to update tag_filter if niche is 'all'
      $(document).on('change', 'input[name="gender_filter"], input[name="follows_gender_filter"], input[name="comments_gender_filter"], input[name="viral_gender_filter"]', function() {
          var section = $(this).closest('.dynamic-form-section');
          var triggerText = section.find('.selected-text-niche').text().trim();
          if (triggerText.includes('All (Mixed Niche)')) {
              section.find('.niche-final-tag-filter').val($(this).val());
          }
      });
  });
</script>

<!-- Hover & Active Shimmer Effects for Kinetic Cards -->
<style>
  .kinetic-card:hover {
    transform: translateY(-3px);
    border-color: var(--premium-gold) !important;
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25) !important;
    background: rgba(255,255,255,0.05) !important;
  }
  
  /* Segmented Controls Styling */
  .premium-segmented-control {
    display: flex;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.1);
  }
  .premium-segmented-control input[type="radio"] {
    display: none;
  }
  .premium-segmented-control label {
    flex: 1;
    text-align: center;
    padding: 10px 5px;
    margin: 0;
    cursor: pointer;
    border-radius: 8px;
    color: rgba(255,255,255,0.6);
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
  }
  .premium-segmented-control input[type="radio"]:checked + label {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.05));
    color: var(--premium-gold);
    border: 1px solid rgba(212, 175, 55, 0.5);
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  }
  .premium-input {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: white !important;
    border-radius: 12px !important;
    padding: 12px 15px !important;
    transition: all 0.3s ease;
  }
  .premium-input:focus {
    border-color: var(--premium-gold) !important;
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.2) !important;
    background: rgba(255,255,255,0.08) !important;
  }

  /* Custom Niche Select Dropdown styling to match premium UI */
  .custom-select-container-niche {
    position: relative;
    width: 100%;
  }
  .custom-select-trigger-niche:hover {
    border-color: var(--premium-gold) !important;
    box-shadow: 0 0 10px rgba(212,175,55,0.15) !important;
  }
  .custom-option-niche {
    padding: 12px 15px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    transition: all 0.2s ease;
  }
  .custom-option-niche:hover {
    background: rgba(212,175,55,0.15);
    color: var(--premium-gold) !important;
    padding-left: 20px !important;
  }
  .custom-select-options-niche::-webkit-scrollbar {
    width: 6px;
  }
  .custom-select-options-niche::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.3);
  }
  .custom-select-options-niche::-webkit-scrollbar-thumb {
    background: var(--premium-gold);
    border-radius: 3px;
  }
</style>
