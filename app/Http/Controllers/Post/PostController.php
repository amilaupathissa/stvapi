<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Post;
use App\Model\MetaData;

class PostController extends Controller
{
    public function base()
    {
        // initiate return variables 
            $all_videos = [];
            $facebook_videos = [];
            $youtube_videos = [];
            $vimeo_videos = [];
        // Get 50 posts from post table
        $posts = Post::orderBy('post_date','desc')
                        ->where('post_status', 'publish')
                        ->where('post_type','avada_portfolio')
                        ->take(50)
                        ->get();
        
        foreach ($posts as $post) {

            // Get pyre_video link 
            $video_url = $this->metadata($post->ID)->video_url;

            // Get image name 
            $image_url = $this->metadata($post->ID)->image_url;


            // Check for sepatate urls
            $has_url = str_contains($video_url,"http");
            if($has_url){
                $sourceType = $this->getSourceType($video_url);

                
                switch ($sourceType) {
                    case 'youtube':
                    $youtube_videos[] = $this->returnArray($post,$sourceType);
                        break;
                    case 'facebook':
                    $facebook_videos[] = $this->returnArray($post,$sourceType);
                        break;
                    case 'vimeo':
                    $vimeo_videos[] = $this->returnArray($post,$sourceType);
                        break;

                }
                $all_videos[] = $this->returnArray($post,$sourceType);
                    
            }
            
            
        }
        
        // return $all_videos;
        return [$all_videos,$facebook_videos,$youtube_videos,$vimeo_videos];

    }

    /**
     * Latest videos 
     */
    public function latest_videos_count($count=10)
    {
        $out=array_slice($this->base()[0], 0, $count);
        return response()->json($out);
    }

    /**
     * Facebook videos
     * 
     * @return Json facebook videos json object array
     */
    public function facebook_videos($count=10)
    {
        $out=array_slice($this->base()[1], 0, $count);
        return response()->json($out);
    }

    /**
     * Youtube Videos
     * 
     * @return JsonObject Array
     */
    public function youtube_videos($count=10)
    {
        $out=array_slice($this->base()[2], 0, $count);
        return response()->json($out);
    }

    /**
     * Vimeo Videos
     * 
     * @return Json vimeo opsts
     */
    public function vimeo_videos($count=10)
    {
        $out=array_slice($this->base()[3], 0, $count);
        return response()->json($out);
    }
    /**
     * Get details of single post
     * 
     * @return single post details
     */
    public function singleVideo($id)
    {
        $post = Post::where('ID',$id)->first();

        // Get pyre_video link of single post
        $video_url = $this->metadata($id)->video_url;

        // Single post details with source type
        $singlePost = $this->returnArray($post, $this->getSourceType($video_url));

        // Add description to single post
        $singlePost['description'] = $this->removeMarkup($post->post_content);

        return $singlePost;


    }


/*
|--------------------------------------------------------------------------
| Privet Functions
|--------------------------------------------------------------------------
|
*/
    /**
     * Remove markups from the description of content
     * 
     * @return String paragraph
     */
    private function removeMarkup($postContent)
    {
        //get text after '[fusion_text'
        $text_after_fusionText = str_after($postContent, '[fusion_text');

        //get content after 'id='
        $text_after_id = str_after($text_after_fusionText, 'id=');
        $text_after_simble = str_after($text_after_id, ']');

        $content = strip_tags(str_before($text_after_simble, '[/fusion_text'));
        //remove front and back new lines and spaces
        $finlaContent = trim(preg_replace(' / \s\s + / ', ' ', $content));  
        
        if(str_contains($finlaContent,'[') || str_contains($finlaContent,']') ){
            return '';
        }else{
            return $finlaContent;
        }

    }

    /**
     *  Template of post object
     * 
     *  @return Array 
     */
    private function returnArray($post,$source_type)
    {
        $image_url = $this->metadata($post->ID)->image_url;
        $video_url = $this->metadata($post->ID)->video_url;
         return  array(
                    'id' => $post->ID,
                    'post_date' => $post->post_date,
                    'post_title' => $post->post_title,
                    'post_url' => 'https://www.shraddha.lk/shraddha-programs/'. $post->post_name,
                    'image_url' => 'https://www.shraddha.lk/wp-content/uploads/'.$image_url,
                    'video_url' => $video_url,
                    'source' => $source_type,
                );
    }

        /**
     * Get metadata from post id
     * 
     * @return requested parameter
     */
    private function metadata($postID)
    {
         // Get thumb_id from metadata table
        $metaData_thumb_id = MetaData::where('post_id', $postID)
            ->where('meta_key', '_thumbnail_id')
            ->value('meta_value');
        // Get pyre_video link from matadata table
        $video_url_noneEdit = MetaData::where('post_id', $postID)
            ->where('meta_key', 'pyre_video')
            ->value('meta_value');
        // Get image name from matadata table
        $image_url = MetaData::where('post_id', $metaData_thumb_id)
            ->where('meta_key', "_wp_attached_file")
            ->value('meta_value');

        // fileter url
        $one = str_after($video_url_noneEdit, 'src=');
        $two = str_before($one, 'frameborder=');
        $three = str_after($two, '"');
        $video_url = str_before($three, '"'); // Final video URL

        return (object)[
            'image_url'=> $image_url,
            'video_url'=> $video_url
        ];

    }

    /**
     * Get source type of the post
     * 
     * @return String sourcetype
     */
    private function getSourceType($video_url)
    {
        $url = $video_url;
        $sourceTypes = [
            'youtube' => "www.youtube.com",
            'facebook' => "www.facebook.com",
            'vimeo' => "vimeo"
        ];
        foreach ($sourceTypes as $type => $source) {
            if(str_contains($url,$source) ){
                return $type;
            }
        }
    }

}
