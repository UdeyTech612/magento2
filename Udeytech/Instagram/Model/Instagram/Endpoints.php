<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Model\Instagram;

class Endpoints
{
    const BASE_URL = 'https://www.instagram.com';
    const ACCOUNT_PAGE = 'https://www.instagram.com/{username}';
    const MEDIA_LINK = 'https://www.instagram.com/p/{code}';
    const ACCOUNT_MEDIAS = 'https://www.instagram.com/{username}/media?max_id={max_id}';
    const ACCOUNT_JSON_INFO = 'https://www.instagram.com/{username}/?__a=1';
    const MEDIA_JSON_INFO = 'https://www.instagram.com/p/{code}/?__a=1';
    const MEDIA_JSON_BY_LOCATION_ID = 'https://www.instagram.com/explore/locations/{{facebookLocationId}}/?__a=1&max_id={{maxId}}';
    const MEDIA_JSON_BY_TAG = 'https://www.instagram.com/explore/tags/{tag}/?__a=1&max_id={max_id}';
    const GENERAL_SEARCH = 'https://www.instagram.com/web/search/topsearch/?query={query}';
    const ACCOUNT_JSON_INFO_BY_ID = 'https://www.instagram.com/query/?q=ig_user({userId}){id,username,external_url,full_name,profile_pic_url,biography,followed_by{count},follows{count},media{count},is_private,is_verified}';
    const LAST_COMMENTS_BY_CODE = 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){comments.last({{count}}){count,nodes{id,created_at,text,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}';
    const COMMENTS_BEFORE_COMMENT_ID_BY_CODE = 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){comments.before({{commentId}},{{count}}){count,nodes{id,created_at,text,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}';
    const LAST_LIKES_BY_CODE = 'https://www.instagram.com/query/?q=ig_shortcode({{code}}){likes{nodes{id,user{id,profile_pic_url,username,follows{count},followed_by{count},biography,full_name,media{count},is_private,external_url,is_verified}},page_info}}';


    public static function getAccountPageLink($username)
    {
        return str_replace('{username}', $username, self::ACCOUNT_PAGE);
    }

    public static function getAccountJsonLink($username)
    {
        return str_replace('{username}', $username, self::ACCOUNT_JSON_INFO);
    }

    public static function getAccountJsonInfoLinkByAccountId($id)
    {
        return str_replace('{userId}', $id, self::ACCOUNT_JSON_INFO_BY_ID);
    }

    public static function getAccountMediasJsonLink($username, $maxId = '')
    {
        $url = str_replace('{username}', $username, self::ACCOUNT_MEDIAS . '&__a=1');
        return str_replace('{max_id}', $maxId, $url);
    }

    public static function getMediaPageLink($code)
    {
        return str_replace('{code}', $code, self::MEDIA_LINK);
    }

    public static function getMediaJsonLink($code)
    {
        return str_replace('{code}', $code, self::MEDIA_JSON_INFO);
    }

    public static function getMediasJsonByLocationIdLink($facebookLocationId, $maxId = '')
    {
        $url = str_replace('{{facebookLocationId}}', $facebookLocationId, self::MEDIA_JSON_BY_LOCATION_ID);
        return str_replace('{{maxId}}', $maxId, $url);
    }

    public static function getMediasJsonByTagLink($tag, $maxId = '')
    {
        $url = str_replace('{tag}', $tag, self::MEDIA_JSON_BY_TAG);
        return str_replace('{max_id}', $maxId, $url);
    }

    public static function getGeneralSearchJsonLink($query)
    {
        return str_replace('{query}', $query, self::GENERAL_SEARCH);
    }

    public static function getLastCommentsByCodeLink($code, $count)
    {
        $url = str_replace('{{code}}', $code, self::LAST_COMMENTS_BY_CODE);
        return str_replace('{{count}}', $count, $url);

    }

    public static function getCommentsBeforeCommentIdByCode($code, $count, $commentId)
    {
        $url = str_replace('{{code}}', $code, self::COMMENTS_BEFORE_COMMENT_ID_BY_CODE);
        $url = str_replace('{{count}}', $count, $url);
        return str_replace('{{commentId}}', $commentId, $url);
    }

    public static function getLastLikesByCodeLink($code)
    {
        $url = str_replace('{{code}}', $code, self::LAST_LIKES_BY_CODE);
        return $url;

    }
}
